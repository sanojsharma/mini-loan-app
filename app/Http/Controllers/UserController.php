<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\User;
use Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeEmail;

class UserController extends Controller
{
    /* Handle Customer Registeration */

    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:4',
            'email' => 'required|email|unique:users,email',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => 'false',
                'errors' => $validator->errors(),
            ]);
        }

       $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt("demo@123")
        ]);

        $token = $user->createToken('MiniApp')->accessToken;
        #Send Welcome email Email
        Mail::to("test@gmail.com")->send(new WelcomeEmail());
        return response()->json([
                'success'=>'true',
                 'user' => ['user_id'=>$user->id,'name'=>$user->name],
                 'token' => $token
            ]
          , 200);
    }

    /* Login Method */

    public function login(Request $request){

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password'=> 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => 'false',
                'errors' => $validator->errors(),
            ],401);
        }

        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){
            $user = Auth::user();
            if( $user->role == 'admin'){
                $token =  $user->createToken('MiniApp',['approve-loan'])->accessToken;
            }else{
                $token =  $user->createToken('MiniApp')->accessToken;
            }
            return response()->json([
                'success'=>'true',
                'user' => ['id'=>$user->id,'name'=>$user->name],
                'token' => $token
            ]
          , 200);
        }else{
            return response()->json([
                'success' => 'false',
                'errors' => 'Invalid Details',
            ],401);
        }
    }


    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'success' => 'false',
            'errors' => 'Successfully logged out',
        ]);
      }
}
