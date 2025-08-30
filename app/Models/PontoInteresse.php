<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PontoInteresse extends Model
{
    use HasFactory;

    protected $primaryKey = 'pk_id_ponto_interesse'; 
    protected $table = 'pontos_interesse';

    protected $fillable = [
        'nome_ponto_interesse',
        'placeid_ponto_interesse',
        'desc_ponto_interesse',
        'latitude',
        'longitude',
        'categoria',
        'hora_ponto_interesse',
        'data_ponto_interesse',
        'fk_id_viagem',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'data_ponto_interesse' => 'date',
    ];

    // Relacionamento com Viagem
    public function viagem()
    {
        return $this->belongsTo(Viagens::class, 'fk_id_viagem', 'pk_id_viagem');
    }
}
