<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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


        return response()->json(['data' => $user], 200);
    }

    public function login(Request $request)
    {
        $data = [
            'user_id'=>$request->user_id,
            'password'=>$request->password
        ];
        if(auth()->attempt($data)) {
            $tokenRequestResult = $this->newToken($data['user_id'],$data['password']);
            return response()->json(
                [
                    'access_token' =>$tokenRequestResult->access_token,
                    'refresh_token'=>$tokenRequestResult->refresh_token
                ]
                ,200);
        } else {
            return response()->json(['error'=>'인증오류'],401);
        }
    }


    public function newToken(String $user_id, String $password) : object
    {
        $client = DB::table('oauth_clients')->where('id', 2)->first();

        $payloadObject = [
            'grant_type' => 'password',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'username' => $user_id,
            'password' => $password,
            'scope' => '',
        ];


        $tokenRequest = Request::create('/oauth/token', 'POST', $payloadObject);
        $tokenRequestResult = json_decode(app()->handle($tokenRequest)->getContent());

        if(isset($tokenRequestResult->message) && $tokenRequestResult->message) {
            return response()->json(['error'=>$tokenRequestResult->message],500);
        }

        return $tokenRequestResult;
    }

    public function userInfo()
    {

        $user = auth()->user();

        return response()->json(['user' => $user], 200);

    }
}
