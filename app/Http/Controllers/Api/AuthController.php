<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $register){
        $register->validate([
            'name'=>'required',
            'email'=>'required|email|unique:users,email',
            'password'=>'required|min:8|confirmed',
        ]);
        $user = User::create($register->only('name','email')+['password'=>$register->password]);
        $token = $user->createToken('token')->plainTextToken;
        return response()->json(['success'=>true,'data'=>['user'=>$user,'token'=>$token],'message'=>'Register ok'],201);
    }

    public function login(Request $register){
        $user = User::where('email',$register->email)->first();
        if(!$user || !Hash::check($register->password,$user->password))
            return response()->json(['success'=>false,'errors'=>['email'=>['Invalid']],'message'=>'Login error'],401);
        $token = $user->createToken('token')->plainTextToken;
        return response()->json(['success'=>true,'data'=>['user'=>$user,'token'=>$token],'message'=>'Login ok']);
    }

    public function logout(Request $register){
        $register->user()->currentAccessToken()->delete();
        return response()->json(['success'=>true,'data'=>null,'message'=>'Logout ok']);
    }

    public function me(Request $register){
        return response()->json(['success'=>true,'data'=>$register->user(),'message'=>'Current user']);
    }
}
