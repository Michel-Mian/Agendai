<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Voos extends Model
{
    protected $primaryKey = 'pk_id_voo'; 
    public function viagem()
    {
        return $this->belongsTo(Viagens::class, 'fk_id_viagem', 'pk_id_viagem');
    }
}
