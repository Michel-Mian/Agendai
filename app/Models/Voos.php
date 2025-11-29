<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voos extends Model
{
    use HasFactory;

    protected $table = 'voos';
    protected $primaryKey = 'pk_id_voo';
    
    protected $fillable = [
        'desc_aeronave_voo',
        'data_hora_partida', 
        'data_hora_chegada',
        'origem_voo',
        'origem_nome_voo',
        'destino_voo', 
        'destino_nome_voo',
        'conexao_voo',
        'conexao_nome_voo',
        'classe_voo',
        'companhia_voo',
        'preco_voo',
        'numero_voo',
        'fk_id_viagem',
    ];

    protected $casts = [
        'data_hora_partida' => 'datetime',
        'data_hora_chegada' => 'datetime',
        'preco_voo' => 'decimal:2',
    ];

    public function viagem()
    {
        return $this->belongsTo(Viagens::class, 'fk_id_viagem', 'pk_id_viagem');
    }
}
