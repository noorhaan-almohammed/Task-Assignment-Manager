<?php

namespace App\Http\Controllers;

use App\Http\Requests\assignRequest;
use App\Http\Requests\StoreTask;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Services\TaskService;

class TaskController extends Controller
{
    protected $taskService;
    public function __construct(TaskService $taskService){
       $this->taskService = $taskService;
    }
    public function index(Request $request) {
        // return $this->taskService->getTasks();
        return $this->taskService->show($request);
    }
    public function show($id)
    {
        return $this->taskService->showTask($id);

        // return $this->taskService->getTasks();
    }
    public function store(StoreTask $request) {
        $validated = $request->validated();
        return $this->taskService->addTask($validated);
     }

    public function assignTask(assignRequest $request, Task $task) {
        $validateId = $request->validatedWithCasts()->toArray();
        return $this->taskService->assignTask($validateId , $task);
    }

    public function updateStatus(Request $request, Task $task) {
        $task->status_id = $request->status_id;
        $task->complete_date = now();

        // Set delivery date if status was changed
        if ($task->isDirty('status_id')) {
            $task->complete_date = now();
        }

        // Rate calculation
        if ($task->complete_date < $task->due_date) {
            $task->rate = 5;
        } elseif ($task->complete_date == $task->due_date) {
            $task->rate = 3;
        } else {
            $task->rate = 1;
        }

        $task->save();

        return response()->json(['message' => 'Task status updated', 'task' => $task], 200);
    }

    public function destroy(Task $task) {
        if (!$task->user_id) {
            $task->delete();
            return response()->json(['message' => 'Task deleted successfully'], 200);
        }

        return response()->json(['message' => 'Task cannot be deleted as it is assigned to an employee'], 403);
    }
}
