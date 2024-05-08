<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\UserService;

class UserController extends Controller
{

    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
   
    public function index()
    {
        //
    }

  
    public function createNewUser(Request $request)
    {
        $data = $request->only(['name', 'email','password']);
        $result = $this->userService->createUser($data);
        return response()->json($result);
    }

    public function login(Request $request)
    {
        $credentials = $request->only(['email','password']);
        $result = $this->userService->login($credentials);
        return $result;
    
    }

    
}
