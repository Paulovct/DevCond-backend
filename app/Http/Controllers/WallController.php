<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Wall;
use App\Models\Walllike;
use App\Models\User;

class WallController extends Controller
{
    public function index(){
        $array = ["error" => ""];

        $user =  Auth::user();

        $walls = Wall::all();
        
        $array["list"] = [];

        forEach($walls as $wall){
            $wall["likes"] = 0;
            $wall["liked"] = false;

            $likes = Walllike::where("id_wall" , $wall["id"])->count();
            $wall["likes"] = $likes;

            $myLike = Walllike::where("id_wall" , $wall["id"])
                ->where("id_user" , $user->id)
            ->first();

            if($myLike){
                $wall["liked"] = true;
            }

            $array["list"][] = $wall;
        }


        return $array;
    }

    public function like(String $id){
        $array = ["error" => ""];

        $user = Auth::user();

        $myLike = Walllike::where("id_wall" , $id)
            ->where("id_user" , $user->id)
        ->first();
        
        if($myLike){
            $myLike->delete();
        } else {
            Walllike::create([
                "id_wall" => $id,
                "id_user" => $user->id
            ]);
        }

        $array["likes"] = Walllike::where("id_wall" , $id)->count();

        return $array;
    }
}
