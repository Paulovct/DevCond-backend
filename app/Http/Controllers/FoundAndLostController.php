<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use App\Models\Foundandlost;

class FoundAndLostController extends Controller
{
    public function index(){
        $array = ["error" =>""];

        $losts = Foundandlost::where("status" , "LOST")
            ->orderBY("id" , "DESC")
        ->get();
        foreach($losts as $lost){
            $lost->date_created = date("d/m/Y" , strtotime($lost->date_created));
            $lost->photo = asset("storage/".$lost->photo);
        };
        $array["lost"] = $losts;

        $founds = Foundandlost::where("status" , "FOUND")
            ->orderBY("id" , "DESC")
        ->get();
        foreach($founds as $found){
            $found->date_created = date("d/m/Y" , strtotime($found->date_created));
            $found->photo = asset("storage/".$found->photo);
        };
        $array["found"] = $founds;

        return $array;
    }

    public function store(Request $req){
        $array = ["error" => ""];

        $validated = Validator::make($req->all() , [
            "description" => "required",
            "where" => "required",
            "photo" => "required|file|mimes:jpg,png"
        ]);

        if($validated->fails()){
            $array["error"] = $validated->errors()->first();
            return $array;
        }

        $data = $req->only("description" , "where");
        $data["status"] = "LOST";
        $data["date_created"] = date("Y-m-d");
        $data["photo"] = $req->file("photo")->store("public");
        $data["photo"] = explode("public/" , $data["photo"])[1];

        Foundandlost::create($data);
        return $array;
    }

    public function update($id ,Request $req){
        $array = ["error" => ""];
        $item = Foundandlost::find($id);

        if(!$item){
            $array["error"] = "ID Inválido";
            return $array;
        }


        if($req->status && in_array($req->status , ["lost" , "found"])){
            $item->status = $req->status;
            $item->save();
        } else {
            $array["error"] = "Status Inválido";
            return $array;
        }


        return $array;
    }
}
