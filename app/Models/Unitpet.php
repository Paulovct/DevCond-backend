<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unitpet extends Model
{
    use HasFactory;
    
    public $timestamps = false;
    
    protected $fillable = [
        "name",
        "race",
        "id_unit"
    ];
}
