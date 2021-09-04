<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PassportAuthController extends Controller
{
    //
    public function register(Request $request)
    {
        $this->validate($request, [
            'user_id'=>'required|unique:users|min:4',
            'email'=>'required|email',
            'password'=>'required|min:8'
        ]);

        $user = User::create([
            'user_id' => $request->user_id,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);

        $token = $user->createToken('Laravel8PassportAuth')->accessToken;

        return response()->json(['token' => $token], 200);
    }

    public function login(Request $request)
    {
        $data = [
            'user_id'=>$request->user_id,
            'password'=>$request->password
        ];
        if(auth()->attempt($data)) {
            $token = auth()->user()->createToken('Laravel8PassportAuth')->accessToken;
            return response()->json(['token'=>$token],200);
        } else {
            return response()->json(['error'=>'Unauthorid'],401);
        }
    }
    public function userInfo()
    {

        $user = auth()->user();

        return response()->json(['user' => $user], 200);

    }
}
