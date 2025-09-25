<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Viajantes extends Model
{
    protected $fillable = [
        'nome_viajante',
        'idade',
        'responsavel_viajante_id',
        'fk_id_viagem'
    ];

    protected $primaryKey = 'pk_id_viajante'; 
    public function viagem()
    {
        return $this->belongsTo(Viagens::class, 'fk_id_viagem', 'pk_id_viagem');
    }
}
