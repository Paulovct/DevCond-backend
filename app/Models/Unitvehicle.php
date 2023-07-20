<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unitvehicle extends Model
{
    use HasFactory;
    
    public $timestamps = false;
    
    protected $fillable = [
        "title",
        "plate",
        "id_unit",
        "color"
    ];
}
