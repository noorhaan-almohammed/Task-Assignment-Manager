<?php

namespace App\Http\Services;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\UnauthorizedException;

class TaskService{

    public function show(Request $request)
    {
        $tasks = Task::query();

        if ($request->has('priority_id')) {
            $tasks->where('priority_id', $request->input('priority_id'));
        }

        if ($request->has('status_id')) {
            $tasks->where('status_id', $request->input('status_id'));
        }
        $result = $tasks->get();

        if ($result->isEmpty()) {
            return response()->json(['message' => 'No Tasks Found'], 404);
        }
        return response()->json($tasks->get());
    }
    // public function getTasks()
    // {
    //     $tasks = Task::all();

    //     $result = $tasks->map(function ($task) {
    //         return [
    //             'id' => $task->id,
    //             'title' => $task->title,
    //             'description' => $task->description,
    //             'status_name' => $task->status->name,
    //             'priority_name' => $task->priority->name,
    //             'user_name' => $task->user->name,
    //             'due_date' => $task->due_date,
    //             'assign_date' => $task->assign_date,
    //             'complete_date' => $task->complete_date,
    //             'execute_time' => $task->execute_time,
    //             'rate' => $task->rate,
    //             'created_at' => $task->created_at,
    //             'updated_at' => $task->updated_at,
    //             'deleted_at' => $task->deleted_at,
    //         ];
    //     });

    //     return response()->json([
    //         'message' => 'Tasks retrieved successfully',
    //         'tasks' => $result
    //     ]);
    // }
    public function addTask(array $data)
    {
        try {
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

     public function showTask($id){
        if (!$id) {
            return response()->json(['message' => 'Task Not Exist'], 404);
        }
       $task = Task::findOrFail($id);
       return response()->json( ['task' => $task], 200);
     }

     /**
     * Assign a task to an employee
     *
     * @param Task $task
     * @param array $data
     * @return JsonResponse
     */
    public function assignTask( array $data,Task $assignedTask): JsonResponse
    {
        try {
            $assignedTask->update([
                'user_id' => $data['user_id'],
                'assign_date' => $data['assign_date'],
                'due_date' => $data['due_date'],
            ]);
           $result = $assignedTask::with(['status', 'priority', 'user'])->get();
            return response()->json([
                'message' => 'Task assigned successfully',
                'task' => $assignedTask
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error assigning task',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
     public function updateTask(array $data , Task $task){
        if (!$task) {
            return response()->json(['message' => 'Task Not Exist'], 404);
        }
        try {
        $task->update($data);
         return response()->json([
            'message' => 'Task Info Updated Successfully',
            'task' => $task
        ], 200);

        } catch (UnauthorizedException $e) {
            return response()->json([
                'message' => 'User does not have the right roles'
            ], 403);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred during the update process',
                'error' => $e->getMessage()
            ], 500);
        }
     }

     public function delete(Task $task){
        if (!$task) {
            return response()->json(['message' => 'Task Not Exist'], 404);
        }
        try {
         $task->delete();
         return response()->json(['message' => 'Task Deleted Succesfully'], 200);
        } catch (UnauthorizedException $e) {
            return response()->json([
                'message' => 'User does not have the right roles'
            ], 403);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred during the delete process',
                'error' => $e->getMessage()
            ], 500);
        }
     }
}
