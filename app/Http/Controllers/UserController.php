<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Services\UserService;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UpdateUserForm;
use App\Http\Requests\StoreUserRequest;

class UserController extends Controller
{
    /**
     * show all users
     */
     protected $userService;
     public function __construct(UserService $userService){
        $this->userService = $userService;
     }
    /**
     * Show All Users
     * @return mixed|\Illuminate\Http\JsonResponse
     */
    public function index()
    {
        return $this->userService->show();
    }

    /**
     * create new user
     */
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        return $this->userService->addUser($validated);
    }

    /**
     * Show A Spicific User
     */
    public function show($id)
    {
        return $this->userService->showUser($id);
    }

    /**
     *
     */
    public function update(UpdateUserForm $request, User $user)
    {
        // $data = $request->validate([
        //     'name' => 'sometimes|string|min:8|max:255',
        //     'email' => 'sometimes|string|email|max:255|unique:users,email',
        //     'password' => 'sometimes|string|min:8',
        //     'role' => 'sometimes|IN:admin,manager,employee',
        // ]);
        // $user->update($data);
        // return $user;
        $data = $request->validated();
        return $this->userService->updateUser($data, $user);
    }

    /**
     * delete user
     */
    public function destroy(User $user)
    {
        return $this->userService->delete($user);
       }
}
