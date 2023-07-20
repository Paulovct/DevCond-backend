<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::table("units")->insert([
            "name" => "APT 100",
            "id_owner" => 1
        ]);

        DB::table("units")->insert([
            "name" => "APT 101",
            "id_owner" => 1
        ]);

        DB::table("units")->insert([
            "name" => "APT 200"
        ]);

        DB::table("units")->insert([
            "name" => "APT 100",
        ]);

        DB::table("areas")->insert([
            "allowed" => 1,
            "title" => "Academia",
            "cover" => "gim.jpg",
            "days" => "1,2,4,5",
            "start_time" => "06:00:00",
            "end_time" => "22:00:00"
        ]);

        DB::table("areas")->insert([
            "allowed" => 1,
            "title" => "Piscina",
            "cover" => "pool.jpg",
            "days" => "1,2,3,4,5",
            "start_time" => "07:00:00",
            "end_time" => "23:00:00"
        ]);
        
        DB::table("areas")->insert([
            "allowed" => 1,
            "title" => "Xurrasqueira",
            "cover" => "barbecue.jpg",
            "days" => "4,5,6",
            "start_time" => "09:00:00",
            "end_time" => "21:00:00"
        ]);

        DB::table("walls")->insert([
            "title" => "aviso de teste",
            "body" => "Xurrasqueira",
            "date_created" => "2020-12-20 15:11:00"
        ]);

    }
}
