<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\LogoutRequest;
use App\Http\Requests\Api\MeRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(RegisterRequest $register){
        $user = User::create($register->only('name','email')+['password'=>$register->password]);
        $token = $user->createToken('token')->plainTextToken;
        return $this->success(['user'=>$user,'token'=>$token],'Opération réussie',201);
    }

    public function login(LoginRequest $register){
        $user = User::where('email',$register->email)->first();
        if(!$user || !Hash::check($register->password,$user->password))
            return $this->error(['email'=>['Invalid']],'Login error',401);
        $token = $user->createToken('token')->plainTextToken;
        return $this->success(['user'=>$user,'token'=>$token],'Opération réussie');
    }

    public function logout(LogoutRequest $register){
        $register->user()->currentAccessToken()->delete();
        return $this->success(null,'Opération réussie');
    }

    public function me(MeRequest $register){
        return $this->success($register->user(),'Opération réussie');
    }
}
