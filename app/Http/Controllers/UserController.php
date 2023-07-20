<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Models\Unit;

class UserController extends Controller
{
    public function index(){
        $array = ["error"=>""];
        $user = Auth::user();

        $array["info"] = $user;
        $array["properties"] = Unit::where("id_owner" , $user->id)->select("id" , "name")->get();

        return $array;
    }

    public function update(Request $req){
        $array = ["error"=>""];
        $user = Auth::user();
        
        $validator = Validator::make($req->all() , [
            "name" => "",
            "email" => "email|unique:users,email",
            "password"=> "" 
        ]);

        if($validator->fails()){
            $array["error"] = $validator->errors()->first();
            return $array;
        }

        if($req->name){
            $user->name =$req->name; 
        }
        if($req->email){
            $user->email = $req->email;
        }

        if($req->password){
            $user->password = password_hash($req->password, PASSWORD_DEFAULT);
        }

        $user->save();

        return $array;
    }
}
