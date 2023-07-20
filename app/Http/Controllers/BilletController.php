<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Billet;
use App\Models\Unit;

class BilletController extends Controller
{
    public function index(Request $req){
        $array = ["error" => ""];

        $property = $req->property;

        if(!$property){
            $array["error"] = "Propriedade necessaria.";
            return $array;
        }

        $unit = Unit::where("id" , $property)->where("id_owner" , Auth::user()->id)->first();

        if(!$unit){
            $array["error"] = "Esta propriedade não é sua ou não existe.";
            return $array;
        }

        $billets = Billet::where("id_unit" , $property)->get();
        $array["list"] = [];

        forEach($billets as $billet){
            $billet["file_url"] = asset("storage/".$billet["file_url"]);
            $array["list"][] = $billet;
        }

        return $array;
    }
}
