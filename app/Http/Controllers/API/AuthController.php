<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function register(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input,[
            'name'=>'required',
            'email'=>'required|email|unique:users,email',
            'password'=>'required',
            'c_password'=>'required_with:password|same:password'
        ]);
        if($validator->fails()){
            return response()->json($validator->messages(),400);
        }
        $data = [
            'name'=>$input['name'],
            'email'=>$input['email'],
            'password'=> bcrypt($input['password'])
        ];
        $user = User::create($data);

        $success['token'] = $user->createToken('MyApp')->plainTextToken;
        $success['user'] = $user->name;

        return response()->json(['status'=>'success','data'=>$success],200);
    }

    public function login(Request $request){
        $input = $request->all();

        if(Auth::attempt(['email'=>$request->email,'password'=>$request->password])){
            $user = Auth::user();
            $success['token'] = $user->createToken('MyApp')->plainTextToken;
            $success['name'] = $user->name;

            return response()->json(['status'=>'success','data'=>$success],200);
        }
        return response()->json(['status'=>'error'],400);
    }
}
