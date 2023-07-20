<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Unit;

class AuthController extends Controller
{
    public function unauthorized(){
        return response()->json(["error" => "Sem Acesso"]);
    }

    public function register(Request $req){
        $array = ["error"=>""];

        $validator = Validator::make($req->only(["name" , "email" , "cpf" , "password" , "password_confirm"]),[
            "name" => "required",
            "email" => "required|email|unique:users,email",
            "cpf" => "required|digits:11|unique:users,cpf",
            "password" => "required",
            "password_confirm" => "required|same:password"
        ]);

        if($validator->fails()){
            $array["error"] = $validator->errors()->first();
            return $array;
        }
        
        $data = $req->only(["name" , "email" , "cpf" , "password"]);
        $data["password"] = password_hash($data["password"], PASSWORD_DEFAULT);
        $newUser = User::create($data);

        $token = Auth::login($newUser);

        if(!$token){
            $array["error"] = "Ocorreu um erro interno";
            return $array;
        }

        $array["token"] = $token;
        $array["user"] = Auth::user();
        $properties = Unit::select(["id" , "name"])->where("id_owner" , $newUser->id)->get();
        $array["user"]["properties"] = $properties;


        return $array;
    }

    public function login(Request $req){
        $array = [];

         $validator = Validator::make($req->only(["cpf" , "password"]),[
            "cpf" => "required|digits:11|exists:users,cpf",
            "password" => "required",
        ]);

        if($validator->fails()){
            $array["error"] = $validator->errors()->first();
            return $array;
        }

        $token = Auth::attempt($req->only(["cpf" , "password"]));


        if(!$token){
            $array["error"] = "CPF ou Senha errados.";
            return $array;
        }

        $array["token"] = $token;
        $array["user"] = Auth::user();
        $properties = Unit::select(["id" , "name"])->where("id_owner" , Auth::user()->id)->get();
        $array["user"]["properties"] = $properties;

        return $array;
    }

    public function validateToken(){
        $array = ["error" => ""];

        $array["user"] = Auth::user();
        $properties = Unit::select(["id" , "name"])->where("id_owner" , Auth::user()->id)->get();
        $array["user"]["properties"] = $properties;

        return $array;
    }

    public function logout(){
        $array = ["error" => ""];
        Auth::logout();
        return $array;
    }
}
