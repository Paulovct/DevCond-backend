<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "id_unit",
        "id_area",
        "reservation_date"
    ];
}
