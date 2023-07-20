<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use App\Models\Warning;
use App\Models\Unit;

class WarningController extends Controller
{
    public function myIndex(Request $req){
        $array = ["error" => ""];

        $property = $req->property;

        if(!$property){
            $array["error"] = "Propriedade necessaria.";
            return $array; 
        }

        $unit = Unit::where("id" , $property)->where("id_owner" , Auth::user()->id)->first();

        if(!$unit){
            $array["error"] = "Esta propriedade não é sua.";
            return $array;
        }

        $warnings = Warning::select()
            ->where("id_unit" , $unit->id)
            ->orderBy("id","DESC")
        ->get();

        $array["list"] = [];

        forEach($warnings as $warning){
            $photos = explode(",", $warning->photos);
            $list = [];
            forEach($photos as $photo){
                if(!empty($photo)){
                    $list[] = asset("storage/".$photo);
                }
            }
            $warning->photos = $list;
            $warning->date_created = date("d/m/Y" , strtotime($warning->date_created));

            $array["list"][] = $warning;
        }


        return $array;
    }

    public function store(Request $req){
        $array = ["error" =>""];

        $validated = Validator::make($req->all() , [
            "title" => "required",
            "property" =>"required"
        ]);

        if($validated->fails()){
            $array["error"] = $validated->errors()->first();
            return $array;
        }

        $newWarn = new Warning();
        $newWarn->id_unit = $req->property; 
        $newWarn->title = $req->title; 
        $newWarn->status = "IN_REVIEW"; 
        $newWarn->date_created = date("Y-m-d");
        if($req->list){
            $photos = [];

            foreach($req->list as $item){
                $url = explode("/" , $item);
                $photos[] = end($url);
            };

            $newWarn->photos = implode("," , $photos);
        } else {
            $newWarn->photos = "";
        }

        $newWarn->save();


        return $array;
    }

    public function storeFile(Request $req)
    {
        $array = ["error"=>""];

        $validated = Validator::make($req->all(),[
            "photo" => "required|file|mimes:jpg,png"
        ]);

        if($validated->fails()){
            $array["error"] = $validated->errors()->first();
            return $array;
        }

        $file = $req->file("photo")->store("public");
        $array["photo"] = asset(Storage::url($file));

        return $array;
    }
}
