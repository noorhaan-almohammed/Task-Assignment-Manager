<?php

namespace App\Http\Services;

use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class TaskService
{
    /**
     * Display a list of tasks with optional filtering based on request parameters.
     *
     * @param Request $request The request containing filter parameters.
     * @return JsonResponse Returns a list of tasks based on the filtering criteria.
     */
    public function show(Request $request)
    {
        $withDeleted = $request->input('withDeleted');

        $tasks = Task::query();

        if ($withDeleted === 'true') {
            $tasks->withTrashed();
        } elseif ($withDeleted === 'false') {
            $tasks->onlyTrashed();
        } else {
            $tasks->withoutTrashed();
        }

        if ($request->has('priority_id')) {
            $tasks->priority($request->input('priority_id'));
        }

        if ($request->has('status_id')) {
            $tasks->status($request->input('status_id'));
        }

        $result = $tasks->get();

        if ($result->isEmpty()) {
            return response()->json(['message' => 'No Tasks Found'], 404);
        }

        return response()->json($result);
    }

    /**
     * Add a new task to the database.
     *
     * @param array $data The validated task data.
     * @return JsonResponse Returns a success message with the created task or an error message if creation fails.
     */
    public function addTask(array $data)
    {
        try {
            if (!isset($data['user_id'])) {
                $data['status_id'] = 1;
            } else {
                $data['assign_date'] = now();
                $data['status_id'] = $data['status_id'] ?? 2;
                if (isset($data['execute_time'])) {
                    $data['due_date'] = now()->addDays($data['execute_time']);
                }
            }
            $task = Task::create($data);
            return response()->json([
                'message' => 'Task created successfully',
                'task' => $task
            ], 201);
        } catch (UnauthorizedException $e) {
            return response()->json([
                'message' => 'User does not have the right roles'
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error creating Task',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retrieve a specific task by its ID.
     *
     * @param int $id The ID of the task to retrieve.
     * @return JsonResponse Returns the specific task or an error message if not found.
     */
    public function showTask($id)
    {
        if (!$id) {
            return response()->json(['message' => 'Task Not Exist'], 404);
        }
        $task = Task::findOrFail($id);
        return response()->json(['task' => $task], 200);
    }

    /**
     * Assign a task to a specific user.
     *
     * @param array $data The validated data containing user ID and assignment details.
     * @param Task $assignedTask The task to be assigned.
     * @return JsonResponse Returns a success message with the updated task or an error message if assignment fails.
     */
    public function assignTask(array $data, Task $assignedTask): JsonResponse
    {
        try {
            if (!$assignedTask) {
                return response()->json(['message' => 'Task not found.'], 404);
            }

            if (!is_null($assignedTask->user_id)) {
                return response()->json(['message' => 'Task is already assigned to a user and cannot be reassigned.'], 403);
            }

            $user = User::find($data['user_id']);
            if (!$user || $user['role'] != "employee") {
                return response()->json(['message' => 'The specified user does not have the required role.'], 403);
            }

            $assignedTask->update([
                'user_id' => $data['user_id'],
                'assign_date' => $data['assign_date'],
                'due_date' => $data['due_date'],
            ]);

            if ($assignedTask->wasChanged('user_id')) {
                $assignedTask->update(['status_id' => 2]);
            }

            return response()->json([
                'message' => 'Task assigned successfully',
                'task' => $assignedTask
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'Task or user not found.', 'error' => $e->getMessage()], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error assigning task', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Unassign a task from a user.
     *
     * @param Task $task The task to be unassigned.
     * @return JsonResponse Returns a success message with the updated task or an error message if unassignment fails.
     */
    public function unAssignTask(Task $task): JsonResponse
    {
        try {
            if (is_null($task->user_id)) {
                return response()->json(['message' => 'Task is not assigned to any user.'], 403);
            }
            $task->update([
                'user_id' => null,
                'assign_date' => null,
                'due_date' => null,
                'status_id' => 1,
            ]);
            return response()->json([
                'message' => 'Task unassigned successfully',
                'task' => $task
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error unassigning task', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the details of an existing task.
     *
     * @param array $data The validated data containing task details to update.
     * @param Task $task The task to be updated.
     * @return JsonResponse Returns a success message with the updated task or an error message if the update fails.
     */
    public function updateTask(array $data, Task $task)
    {
        if (!$task) {
            return response()->json(['message' => 'Task does not exist'], 404);
        }
        if ($task->status_id === 3 || $task->status_id === 4) {
            return response()->json([
                'message' => 'Task is already completed and cannot be updated.',
            ], 400);
        }
        try {
            if (!is_null($task->user_id)) {
                $updates = [];
                if (isset($data['execute_time'])) {
                    $updates['execute_time'] = $data['execute_time'];
                    $updates['due_date'] = now()->addDays($data['execute_time']);
                }

                if (isset($data['priority_id'])) {
                    $updates['priority_id'] = $data['priority_id'];
                }

                if (isset($data['user_id'])) {
                    return response()->json([
                        'message' => 'Task is already assigned to a user and cannot be reassigned.',
                    ], 400);
                }

                if (!empty($updates)) {
                    $task->update($updates);
                    return response()->json([
                        'message' => 'Task updated successfully with execute_time and priority_id adjustments. Other data has been ignored.',
                        'task' => $task
                    ], 200);
                }

                return response()->json(['message' => 'No valid fields were provided for update'], 400);

            } else {
                $updates = [];
                if (isset($data['title'])) {
                    $updates['title'] = $data['title'];
                }

                if (isset($data['description'])) {
                    $updates['description'] = $data['description'];
                }

                if (isset($data['priority_id'])) {
                    $updates['priority_id'] = $data['priority_id'];
                }

                if (isset($data['execute_time'])) {
                    $updates['execute_time'] = $data['execute_time'];
                    $updates['due_date'] = now()->addDays($data['execute_time']);
                }

                if (!empty($updates)) {
                    $task->update($updates);
                }

                if (isset($data['user_id'])) {
                    if (!is_null($task->user_id)) {
                        return response()->json([
                            'message' => 'Task is already assigned to a user and cannot be reassigned.',
                        ], 403);
                    }

                    $user = User::find($data['user_id']);
                    if (!$user || $user->role !== 'employee') {
                        return response()->json(['message' => 'The specified user does not have the required role.'], 403);
                    }

                    $task->update([
                        'user_id' => $data['user_id'],
                        'assign_date' => now(),
                        'due_date' => now()->addDays($task['execute_time']),
                        'status_id' => 2,
                    ]);

                    return response()->json([
                        'message' => 'Task assigned successfully with updated user information',
                        'task' => $task
                    ], 200);
                }

                if (!empty($updates)) {
                    return response()->json([
                        'message' => 'Task updated successfully with new details',
                        'task' => $task
                    ], 200);
                }

                return response()->json(['message' => 'No valid fields were provided for update'], 400);
            }

        } catch (UnauthorizedException $e) {
            return response()->json(['message' => 'User does not have the right roles'], 403);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred during the update process', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Update the status of a task.
     *
     * @param array $data The validated data containing the new status.
     * @param Task $task The task whose status is to be updated.
     * @return JsonResponse Returns a success message with the updated task or an error message if the update fails.
     */
    public function updateStatusTask(array $data, Task $task)
    {
        if (auth()->id() !== $task->user_id || $task->user_id === null) {
            return response()->json(['message' => 'You are not authorized to update this task.'], 403);
        }
        if ($task->status_id !== 2) {
            return response()->json(['message' => 'You cannot update this task.'], 403);
        }

        $status = $data['status_id'];
        $completeDate = now();
        $dueDate = $task->due_date;
        $rate = null;

        if ($task->isDirty('status_id')) {
            return response()->json(['message' => 'No changes detected in status.'], 400);
        }

        if ($status == 3) {
            $daysDifference = $completeDate->diffInDays($dueDate, false);
            if ($daysDifference > 0) {
                $rate = max(3, 5 - ($daysDifference * 0.1));
            } elseif ($daysDifference < 0) {
                $rate = min(5, 3 + (abs($daysDifference) * 0.1));
            } else {
                $rate = 3;
            }
        } elseif ($status == 4) {
            $rate = 1;
        }

        $task->update([
            'status_id' => $status,
            'complete_date' => $completeDate,
            'rate' => $rate
        ]);

        $user = User::find($task->user_id);
        $userRate = $user->averageTaskRate()->first();
        $user->update(['user_rate' => $userRate]);

        return response()->json([
            'message' => 'Task status updated successfully',
            'task' => $task,
            'rate' => $rate
        ], 200);
    }

    /**
     * Delete a task from the database.
     *
     * @param Task $task The task to be deleted.
     * @return JsonResponse Returns a success message if the task is deleted or an error message if deletion fails.
     */
    public function delete(Task $task)
    {
        if (!$task) {
            return response()->json(['message' => 'Task Not Exist'], 404);
        }

        try {
            if ($task->user_id) {
                return response()->json(['message' => 'Task cannot be deleted as it is assigned to a user'], 403);
            }

            $task->delete();

            return response()->json(['message' => 'Task Deleted Successfully'], 200);
        } catch (UnauthorizedException $e) {
            return response()->json(['message' => 'User does not have the right roles'], 403);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred during the delete process', 'error' => $e->getMessage()], 500);
        }
    }
}
