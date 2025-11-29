<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Objetivos extends Model
{
    protected $fillable = [
        'nome',
        'fk_id_viagem'
    ];
    
    public function viagem()
    {
        return $this->belongsTo(Viagens::class, 'fk_id_viagem', 'pk_id_viagem');
    }
}
