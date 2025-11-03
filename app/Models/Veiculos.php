<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Veiculos extends Model
{
    protected $table = 'veiculos';
    protected $primaryKey = 'pk_id_veiculo';
    
    protected $fillable = [
        'fk_id_viagem',
        'nome_veiculo',
        'categoria',
        'imagem_url',
        'passageiros',
        'malas',
        'ar_condicionado',
        'cambio',
        'quilometragem',
        'diferenciais',
        'tags',
        'endereco_retirada',
        'tipo_local',
        'nome_local',
        'locadora_nome',
        'locadora_logo',
        'avaliacao_locadora',
        'preco_total',
        'preco_diaria',
        'link_reserva',
        'is_selected'
    ];
    
    protected $casts = [
        'ar_condicionado' => 'boolean',
        'is_selected' => 'boolean',
        'diferenciais' => 'array',
        'tags' => 'array',
        'passageiros' => 'integer',
        'avaliacao_locadora' => 'decimal:1',
        'preco_total' => 'decimal:2',
        'preco_diaria' => 'decimal:2'
    ];
    
    /**
     * Relacionamento com Viagens
     */
    public function viagem()
    {
        return $this->belongsTo(Viagens::class, 'fk_id_viagem', 'pk_id_viagem');
    }
}
