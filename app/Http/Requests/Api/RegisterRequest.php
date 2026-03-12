<?php

namespace App\Http\Requests\Api;

class RegisterRequest extends BaseApiRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ];
    }
}
