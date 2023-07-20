<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FoundAndLost extends Model
{
    use HasFactory;
    
    public $table = "foundandlost";

    public $timestamps = false;

    protected $fillable = [
        "description",
        "photo",
        "where",
        "date_created"
    ];

}
