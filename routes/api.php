<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BilletController;
use App\Http\Controllers\DocController;
use App\Http\Controllers\FoundAndLostController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WallController;
use App\Http\Controllers\WarningController;
use App\Http\Controllers\UnitController;

Route::get("/ping" , function(){
    return response()->json(["pong" => true]);
});


Route::get("/401" , [AuthController::class , "unauthorized"])->name("login");

Route::post("/auth/login" , [AuthController::class , "login"]); 
Route::post("/auth/register" , [AuthController::class , "register"]);

Route::middleware("auth:api")->group(function(){
    
    Route::post("/auth/validate",[AuthController::class , "validateToken"]);
    Route::post("/auth/logout",[AuthController::class , "logout"]);

    Route::get("/walls" , [WallController::class , "index"]);
    Route::post("/wall/{id}/like" , [WallController::class , "like"]);

    Route::get("/docs" , [DocController::class , "index"]);

    Route::get("/warnings" , [WarningController::class , "myIndex"]);
    Route::post("/warning" , [WarningController::class , "store"]);
    Route::post("/warning/file" , [WarningController::class , "storeFile"]);

    Route::get("/billets" , [BilletController::class , "index"]);

    Route::get("/foundandlost" , [FoundAndLostController::class , "index"]);
    Route::post("/foundandlost" , [FoundAndLostController::class , "store"]);
    Route::put("/foundandlost/{id}" , [FoundAndLostController::class , "update"]);

    Route::get("/unit/{id}" , [UnitController::class , "show"]);
    Route::post("unit/{id}/addperson" , [UnitController::class , "addPerson"]);
    Route::post("unit/{id}/addpet" , [UnitController::class , "addPet"]);
    Route::post("unit/{id}/addvehicle" , [UnitController::class , "addVehicle"]);
    Route::post("unit/{id}/removeperson" , [UnitController::class , "removePerson"]);
    Route::post("unit/{id}/removepet" , [UnitController::class , "removePet"]);
    Route::post("unit/{id}/removevehicle" , [UnitController::class , "removeVehicle"]);

    Route::get("/reservations" , [ReservationController::class , "index"]);
    Route::post("/reservation/{id}" , [ReservationController::class , "store"]);
    
    Route::get("/reservation/{id}/disableddates" ,[ReservationController::class , "getDisabledDates"]);
    Route::get("/reservation/{id}/times" ,[ReservationController::class , "getTimes"]);

    Route::get("/myreservations" , [ReservationController::class , "myIndex"]);
    Route::delete("/myreservation/{id}" , [ReservationController::class , "delete"]);

    Route::get("/me" , [UserController::class , "index"]);
    Route::put("/me" , [UserController::class , "update"]);
}); 
