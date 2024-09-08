<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Http\Requests\StoreTask;
use App\Http\Requests\UpdateTask;
use App\Http\Services\TaskService;
use Illuminate\Validation\UnauthorizedException;

class TaskController extends Controller
{
    /**
     * Show all tasks
     */
    protected $taskService;
    public function __construct(TaskService $taskService){
       $this->taskService = $taskService;
    }
   /**
    * Show All Tasks
    * @return mixed|\Illuminate\Http\JsonResponse
    */
   public function index()
   {
      return $this->taskService->show();
   }

   /**
    * create new Task
    */
   public function store(StoreTask $request)
   {
       $validated = $request->validated();
       return $this->taskService->addTask($validated);
   }

   /**
    * Show A Spicific Task
    */
   public function show($id)
   {
       return $this->taskService->showTask($id);
   }

   /**
    * Update a spicific Task
    */
   public function update(UpdateTask $request, Task $task)
   {
        $data = $request->validated();
        return $this->taskService->updateTask($data, $task);
   }

   /**
    * delete task
    */
   public function destroy(Task $task)
   {
      return $this->taskService->delete($task);
   }
}
