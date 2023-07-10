<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Models\User;

class UserController extends Controller
{
    private $userModel;

    public function __construct(User $userModel){
        $this->userModel = $userModel;
    }

    public function addUser(CreateUserRequest $request){
        dd($request);
        try {
            dd($request);
            $request = $request->validated();
            $this->userModel->create($request);
        }catch (\Exception $e){
            dd($e->getMessage());
        }
    }
}
