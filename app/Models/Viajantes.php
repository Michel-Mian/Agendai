<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Viajantes extends Model
{
    protected $fillable = [
        'nome',
        'idade',
        'responsavel_viajante_id',
        'fk_id_viagem',
        'observacoes'
    ];

    protected $primaryKey = 'pk_id_viajante'; 
    public function viagem()
    {
        return $this->belongsTo(Viagens::class, 'fk_id_viagem', 'pk_id_viagem');
    }

    // Relacionamento com seguros - cada viajante pode ter múltiplos seguros
    public function seguros()
    {
        return $this->hasMany(Seguros::class, 'fk_id_viajante', 'pk_id_viajante');
    }
}
