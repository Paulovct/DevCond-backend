<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unitpeople extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        "name",
        "birthdate",
        "id_unit" 
    ];
}
