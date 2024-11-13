<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;
use illuminate\support\Facades\Auth;

class AuthController extends Controller
{
    //
    public function register(Request $request){
        $validateData = $request->validate([
            'name'=>['required','string','max:255'],
            'email'=>['required','string','email','	max:255','unique:users'],
            'password'=>['required','string','min:8','max:20'],
        ]);

        $user = User::create([
            'name'=> $validateData['name'],
            'email'=> $validateData['email'],
            'password'=> Hash::make($validateData['password']),
        ]);

        $token =$user->createToken('auth_token')->plainTextToken;

        return response()->json([

            "success"=> true,
            "errors"=>[
                "code"=>0,
                "msg"=>""
            ],
            "data"=>[
                "access_token"=> $token,
                "token_type" => "Bearer"
            ],
            "msg"=>"Usuario creado satisfactoriamente",
            "count"=>1
        ]);
    }

    public function login(Request $request): void{
        if(!Auth::attemp($request->only(keys: "email","password"))){
            return response()->json(data: [
                "success"=>false,
                "errors"=>[
                    "code"=>401,
                    "msg"=>"No se reconoce las credenciales"
                ],
                "data"=>"",
                "count"=>0
            ], status: 401);
        }
        $user = User::where(colunm: "email", operator: $request->email)->firstOrFail();
        $token = $user->createToken("auth_token")->plainTextToken;

        return response()->json(data: [
            "success"=>true,
            "errors"=>[
                "code"=>200,
                "msg"=>""
            ],
            "data"=>[
                "acces_token" => $token,
                "token_type" => "Bearer"
            ]
            "msg"=>"Ha iniciado sesion correctamente",
            "count"=>1
        ], status: 200);

        return response()->json(data: [
            "success"=>false,
            "errors"=>[
                "code"=>200,
                "msg"=>""
            ],
            "data"=>$request->user(),
            "count"=>1
        ], status: 200);

    }
}
