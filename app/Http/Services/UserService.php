<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Validation\UnauthorizedException;

class UserService{

    public function show(){
      $users = User::paginate(); // 15 by default
      return response()->json(['users' => $users], 200);
    }
     public function addUser(array $data){
        try{
            $user = User::create($data);
            return response()->json(['message' => 'User created succesfully',
                                        'user' => $user], 201);
          }catch (UnauthorizedException $e) {
            return response()->json([
                'message' => 'User does not have the right roles'
            ], 403);
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Error creating User',
                    'error' => $e->getMessage()
                ], 500);
        }
     }
     public function showUser($id){
       if (!$id) {
        return response()->json(['message' => 'User Not Exist'], 404);
       }
        $user = User::findOrFail($id);
        return response()->json( ['user' => $user], 200);
     }

     public function updateUser(array $data , User $user){
        if (!$user) {
            return response()->json(['message' => 'User Not Exist'], 404);
        }
        try {
        $user->update($data);
         return response()->json([
            'message' => 'User Info Updated Successfully',
            'user' => $user
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

     public function delete(User $user){
        if (!$user) {
            return response()->json(['message' => 'User Not Exist'], 404);
        }
        try {
         $user->delete();
         return response()->json(['message' => 'User Deleted Succesfully'], 200);
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
