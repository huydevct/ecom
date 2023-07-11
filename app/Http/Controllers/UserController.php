<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Models\User;
use http\Env\Response;

class UserController extends Controller
{
    private $userModel;

    public function __construct(User $userModel){
        $this->userModel = $userModel;
    }

//    public function addUser(CreateUserRequest $request){
//        try {
//            $request = $request->validated();
//            $this->userModel->addUser($request);
//            return response()->json('create user success');
//        }catch (\Exception $e){
//            dd($e->getMessage());
//        }
//    }
}
