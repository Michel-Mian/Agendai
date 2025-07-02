<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Viajantes extends Model
{
    protected $primaryKey = 'pk_id_viajante'; 
    public function viagem()
    {
        return $this->belongsTo(Viagens::class, 'fk_id_viagem', 'pk_id_viagem');
    }
}
