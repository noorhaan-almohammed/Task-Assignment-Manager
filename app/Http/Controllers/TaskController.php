<?php
namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTask;
use App\Http\Services\TaskService;
use App\Http\Requests\assignRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\UpdateStatusRequest;

class TaskController extends Controller
{
    protected $taskService;

    /**
     * Constructor to inject TaskService.
     *
     * @param TaskService $taskService The service to handle task-related operations.
     */
    public function __construct(TaskService $taskService){
        $this->taskService = $taskService;
    }

    /**
     * Display a list of tasks, with optional filtering.
     *
     * @param Request $request The request containing any filter parameters.
     * @return \Illuminate\Http\JsonResponse Returns the list of tasks.
     */
    public function index(Request $request) {
        return $this->taskService->show($request);
    }

    /**
     * Show a specific task by its ID.
     *
     * @param int $id The ID of the task to retrieve.
     * @return \Illuminate\Http\JsonResponse Returns the specific task.
     */
    public function show($id)
    {
        return $this->taskService->showTask($id);
    }

    /**
     * Store a newly created task in the database.
     *
     * @param StoreTask $request The validated request containing task details.
     * @return \Illuminate\Http\JsonResponse Returns the newly created task.
     */
    public function store(StoreTask $request) {
        $validated = $request->validated();
        return $this->taskService->addTask($validated);
    }

    /**
     * Assign a task to a specific user.
     *
     * @param assignRequest $request The request containing the user ID to assign the task to.
     * @param Task $task The task to be assigned.
     * @return \Illuminate\Http\JsonResponse Returns the updated task with the assigned user.
     */
    public function assignTask(assignRequest $request, Task $task) {
        $validateId = $request->validatedWithCasts()->toArray();
        return $this->taskService->assignTask($validateId, $task);
    }

    /**
     * Unassign a task from a user (set the task as unassigned).
     *
     * @param Task $task The task to be unassigned.
     * @return \Illuminate\Http\JsonResponse Returns the updated task after unassignment.
     */
    public function unAssignTask(Task $task) {
        return $this->taskService->unAssignTask($task);
    }

    /**
     * Update a task's details.
     *
     * @param UpdateTaskRequest $request The request containing the updated task data.
     * @param Task $task The task to be updated.
     * @return \Illuminate\Http\JsonResponse Returns the updated task.
     */
    public function update(UpdateTaskRequest $request, Task $task){
        $data = $request->validated();
        return $this->taskService->updateTask($data, $task);
    }

    /**
     * Update the status of a task.
     *
     * @param UpdateStatusRequest $request The request containing the new task status.
     * @param Task $task The task whose status needs to be updated.
     * @return \Illuminate\Http\JsonResponse Returns the updated task with the new status.
     */
    public function updateStatusTask(UpdateStatusRequest $request, Task $task) {
        $data = $request->validated();
        return $this->taskService->updateStatusTask($data, $task);
    }

    /**
     * Delete a task if it is not assigned to any user.
     *
     * @param Task $task The task to be deleted.
     * @return \Illuminate\Http\JsonResponse Returns a success or error message depending on the deletion outcome.
     */
    public function destroy(Task $task) {
        if (!$task->user_id) {
            $task->delete();
            return response()->json(['message' => 'Task deleted successfully'], 200);
        }

        return response()->json(['message' => 'Task cannot be deleted as it is assigned to an employee'], 403);
    }
}
