<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


use App\Models\Unit;
use App\Models\Unitpeople;
use App\Models\Unitvehicle;
use App\Models\Unitpet;

class UnitController extends Controller
{
    public function show($id){
        $array = ["error" => ""];

        $unit = Unit::find($id);
        if(!$unit){
            $array["error"] = "ID Inválida";
            return $array;
        }

        $peoples = Unitpeople::where("id_unit" , $unit->id)->get();
        foreach($peoples as $p){
            $p->birthdate = date("d/m/Y", strtotime($p->birthdate));
        }
        $pets = Unitpet::where("id_unit" , $unit->id)->get();
        $vehicles = Unitvehicle::where("id_unit" , $unit->id)->get();

        $array["peoples"] = $peoples;
        $array["pets"] = $pets;
        $array["vehicles"] = $vehicles;

        return $array;
    }

    public function addPerson($id, Request $req){
        $array = ["error" => ""];

        $validated = Validator::make($req->all(),[
            "name" => "required",
            "birthdate" => "required|date"
        ]);

        if($validated->fails()){
            $array["error"] = $validated->errors()->first();
            return $array;
        }

        $data = $req->only(["name" , "birthdate"]);
        $data["id_unit"] = $id;
        Unitpeople::create($data);
        
        return $array; 
    }

    public function addVehicle($id, Request $req){
        $array = ["error" => ""];

        $validated = Validator::make($req->all(),[
            "title" => "required",
            "color" => "required",
            "plate" => "required"
        ]);

        if($validated->fails()){
            $array["error"] = $validated->errors()->first();
            return $array;
        }

        $data = $req->only(["title" , "plate" , "color"]);
        $data["id_unit"] = $id;
        Unitvehicle::create($data);
        
        return $array; 
    }

    public function addPet($id, Request $req){
        $array = ["error" => ""];

        $validated = Validator::make($req->all(),[
            "name" => "required",
            "race" => "required",
        ]);

        if($validated->fails()){
            $array["error"] = $validated->errors()->first();
            return $array;
        }

        $data = $req->only(["name" , "race"]);
        $data["id_unit"] = $id;
        Unitpet::create($data);
        
        return $array; 
    }

    public function removePerson($id , Request $req){
        $array = ["error" => ""];

        $personId = $req->id;
        if(!$personId){
            $array["error"] = "ID Inválida";
            return $array;
        }
        $person = Unitpeople::where("id_unit" , $id)
            ->where("id" , $personId)
        ->first();

        if($person){
            $person->delete();
        } else {
            $array["error"] = "Pessoa Inexistente";
        }

        return $array;
    }

    public function removePet($id , Request $req){
        $array = ["error" => ""];

        $petId = $req->id;
        if(!$petId){
            $array["error"] = "ID Inválida";
            return $array;
        }
        $pet = Unitpet::where("id_unit" , $id)
            ->where("id" , $petId)
        ->first();

        if($pet){
            $pet->delete();
        } else {
            $array["error"] = "Animal Inexistente";
        }

        return $array;
    }

    public function removeVehicle($id , Request $req){
        $array = ["error" => ""];

        $vehicleId = $req->id;
        if(!$vehicleId){
            $array["error"] = "ID Inválida";
            return $array;
        }
        $vehicle = Unitvehicle::where("id_unit" , $id)
            ->where("id" , $vehicleId)
        ->first();

        if($vehicle){
            $vehicle->delete();
        } else {
            $array["error"] = "Veículo Inexistente";
        }

        return $array;
    }
}
