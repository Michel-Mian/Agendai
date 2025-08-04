<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Airport extends Model
{
    protected $fillable = [
        'name',
        'city',
        'country',
        'iata_code',
    ];
}
