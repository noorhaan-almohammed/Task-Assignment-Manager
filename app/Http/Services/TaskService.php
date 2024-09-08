<?php

namespace App\Http\Services;

use App\Models\Task;
use Illuminate\Validation\UnauthorizedException;

class TaskService{

    public function show()
    {
      $tasks = Task::paginate();
      return response()->json(['tasks' => $tasks ,
                            //    'status_name' => $tasks->status_name,
                            //    'priority_name' => $tasks->priority_name
                            ], 200);
    }
     public function addTask(array $data){
      try{
        $task = Task::create($data);
        return response()->json(['message' => 'Task created succesfully',
                                    'task' => $task], 201);
      }catch (UnauthorizedException $e) {
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
