<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Models\Area;
use App\Models\Areadisabledday;
use App\Models\Reservation;
use App\Models\Unit;

class ReservationController extends Controller
{
    public function index(){
        $array = ["error" => "" , "list" => []];
        $days = ["Dom" , "Seg" , "Ter" , "Qua" , "Qui" , "Sex", "Sáb"];

        $areas = Area::where("allowed" , 1)->get();

        foreach($areas as $area){
            $dayList = explode(",", $area->days);

            $dayGroups = [];
            //first
            $lastDay = intval(current($dayList));
            $dayGroups[] = $days[$lastDay];
            array_shift($dayList);

            //middles
            foreach($dayList as $day){
                if(intval($day) != $lastDay + 1){
                    $dayGroups[] = $days[$lastDay];
                    $dayGroups[] = $days[$day];
                }
                $lastDay = intval($day);
            }

            //lasts
            $dayGroups[] = $days[end($dayList)];

            $dates = "";
            $close = 0;
            foreach($dayGroups as $group){
                if($close == 0){
                    $dates .= $group;
                } else {
                    $dates .= "-".$group.",";
                }

                $close = 1 - $close;
            }

            $dates = explode("," , $dates);
            array_pop($dates);

            //time
            $start = date("H:i" , strtotime($area->start_time));
            $end = date("H:i" , strtotime($area->end_time));

            foreach($dates as $dKey => $dValue){
                $dates[$dKey] .= " ".$start." ás ".$end; 
            }

            $array["list"][] = [
                "id" => $area->id,
                "cover" => asset("storage/".$area->cover),
                "title" => $area->title,
                "dates" => $dates
            ];
        }

        return $array;
    }

    public function getDisabledDates($id){
        $array = ["error"=> "" , "list"=>[]];

        $area = Area::find($id);
        if(!$area){
            $array["error"] = "Área Inexistente";
            return $array;
        }

        $disabledDays = Areadisabledday::where("id_area" , $id)->get();
        foreach($disabledDays as $day){
            $array["list"][] = $day;
        }

        //notAllowedDates
        $allowedDays = explode("," , $area->days);
        $offDays = [];

        for($q=0 ; $q < 7; $q++){
            if(!in_array($q , $allowedDays)){
                $offDays[] = $q;
            }
        }
        

        //disabledDays 3 months forward
        $start = time();
        $end = strtotime("+3 months");
        
        for($current = $start; $current < $end; $current = strtotime("+1 day",$current)){
            $wd = date("w" , $current);
            if(in_array($wd , $offDays)){
                $array["list"][] = date("Y-m-d" , $current);
            }
        }


        return $array;
    }

    public function getTimes($id , Request $req){
        $array = ["error"=>"", "list"=> []];

        $validated = Validator::make($req->all(),[
            "date" => "required|date_format:Y-m-d"
        ]);
        if($validated->fails()){
            $array["error"] = $validated->errors()->first();
            return $array;
        }

        $area = Area::find($id);
        if(!$area){
            $array["error"] = "Área Inexistente";
            return $array;
        }

        $can = true;

        $existingDisabledDay = Areadisabledday::where("id_area" , $id)
            ->where("day" , $req->date)
        ->first();
        if($existingDisabledDay){
            $can = false;
        }

        $allowedDays = explode("," , $area->days);
        $weekDay = date("w" , strtotime($req->date));
        if(!in_array($weekDay , $allowedDays)){
            $can = false;
        }

        if($can){
            $times = [];
            $start = strtotime($area->start_time);
            $end = strtotime($area->end_time);

            for($lastTime = $start; $lastTime < $end; $lastTime = strtotime("+1 hour" , $lastTime)){
                $times[] = $lastTime; 
            }

            $timeList = [];
            foreach($times as $time){
                $timeList[] = [
                    "id" => date("H:i:s" , $time),
                    "title" => date("H:i" , $time)." - ".date("H:i" , strtotime("+1 hour" , $time))
                ];
            }

            //filtering by reservations
            $reservations = Reservation::where("id_area" , $id)
                ->whereBetween("reservation_date" , [
                    $req->date." 00:00:00",
                    $req->date." 23:59:59"
                ])
            ->get();

            $toRemove = [];
            foreach($reservations as $reservation){
                $time = date("H:i:s" , strtotime($reservation->reservation_date));
                $toRemove[] = $time;
            }

            foreach($timeList as $timeItem){
                if(!in_array($timeItem["id"] , $toRemove)){
                    $array["list"][] = $timeItem;
                }
            }
        }


        return $array;
    }


    public function store($id , Request $req){
        $array = ["error" => ""];

        $validated = Validator::make($req->all(),[
            "date" => "required|date_format:Y-m-d",
            "time" => "required|date_format:H:i:s",
            "property" => "required|exists:areas,id"
        ]);

        if($validated->fails()){
            $array["error"] = $validated->errors()->first();
            return $array;
        }

        $data = $req->only(["date" , "time" , "property"]);

        $area = Area::find($id);
        if(!$area){
            $array["error"] = "Dados Incorretos";
            return $array;
        }

        $can = true;

        $weekDay = date("w" , strtotime($data["date"]));

        //allowedDays
        $allowedDays = explode(",",$area->days);
        if(!in_array($weekDay , $allowedDays)){
            $can = false;
        } else {
            $start = strtotime($area->start_time);
            $end = strtotime("-1 hour" , strtotime($area->end_time));
            $revTime = strtotime($data["time"]);
            if($revTime < $start || $revTime > $end){
                $can = false;
            }

        }

        //dissabledDays
        $existingDisabledDay = Areadisabledday::where("id_area" , $id)
            ->where("day" , $data["date"])
        ->first();
        if($existingDisabledDay){
            $can = false;
        }

        //reservation
        $existingReservation = Reservation::where("id_area" , $id)
            ->where("reservation_date" , $data["date"]." ".$data["time"])
        ->first();
        if($existingReservation){
            $can = false;
        }


        if($can){
            $newReservation = Reservation::create([
                "id_unit" => $data["property"],
                "id_area" => $id,
                "reservation_date" => $data["date"]." ".$data["time"]
            ]);
            $array["reservation"] = $newReservation;
        } else {
            $array["error"] = "Reserva não permitida neste dia/horario";
        }

        return $array;
    }

    public function myIndex(Request $req){
        $array = ["error" => "" , "list"=> []];

        if(!$req->property){
            $array["error"] = "Propriedade Necessaria";
            return $array;
        }

        $user = Auth::user();
        $unit = Unit::where("id", $req->property)
            ->where("id_owner" , $user->id)
        ->first();
        if(!$unit){
            $array["error"] = "Propriedade Inválida";
            return $array;
        }

        $reservations = Reservation::where("id_unit" , $unit->id)
            ->orderBy("id","DESC")
        ->get();

        foreach($reservations as $reservation){
            $area = Area::find($reservation->id_area);

            $dateRev = date("d/m/Y H:i:s" , strtotime($reservation->reservation_date));
            $afterTime = date("H:i" , strtotime("+1 hour" , strtotime($reservation->reservation_date)));
            $dateRev .= " á ".$afterTime;

            $array["list"][] = [
                "id" => $reservation->id,
                "id_area" => $reservation->id_area,
                "title" => $area->title,
                "cover" => asset("storage/".$area->cover),
                "datereserved" => $dateRev
            ];
        }

        return $array;
    }


    public function delete($id){
        $array = ["error"=>""];

        $user = Auth::user();
        $reservation = Reservation::find($id);
        if(!$reservation){
            $array["error"] = "Reserva Inexistente";
            return $array;
        }

        $unit = Unit::where("id_owner" , $user->id)
            ->where("id", $reservation->id_unit)
        ->first();

        if(!$unit){
            $array["error"] = "Sem Permissão";
            return $array;
        }

        $reservation->delete();

        return $array;
    }
}
