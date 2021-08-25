<?php

namespace App\Http\Controllers;


use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validation = $request -> validate([
            'user_id' => 'required|min:6|max:12|unique:users|string',
            'password' => 'required|min:10|max:16|confirmed',
            'email' => 'required|email'
        ]);

        return [
            User::create([
                'user_id' => $validation['user_id'],
                'password' => Hash::make($validation['password']),
                'email' => $validation['email']
            ])
        ];
    }
}
