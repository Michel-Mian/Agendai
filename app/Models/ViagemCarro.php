<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ViagemCarro extends Model
{
    protected $table = 'viagem_carro';
    protected $primaryKey = 'pk_id_viagem_carro';
    
    protected $fillable = [
        'fk_id_viagem',
        'autonomia_veiculo_km_l',
        'tipo_combustivel',
        'preco_combustivel_litro',
        'distancia_total_km',
        'pedagio_estimado',
        'pedagio_oficial',
        'combustivel_estimado_litros',
        'custo_combustivel_estimado',
        'duracao_segundos',
        'rota_detalhada'
    ];
    
    protected $casts = [
        'autonomia_veiculo_km_l' => 'decimal:2',
        'preco_combustivel_litro' => 'decimal:2',
        'distancia_total_km' => 'decimal:2',
        'pedagio_estimado' => 'decimal:2',
        'pedagio_oficial' => 'boolean',
        'combustivel_estimado_litros' => 'decimal:2',
        'custo_combustivel_estimado' => 'decimal:2',
        'duracao_segundos' => 'integer',
        'rota_detalhada' => 'array'
    ];
    
    /**
     * Relacionamento com a viagem (1:1)
     */
    public function viagem(): BelongsTo
    {
        return $this->belongsTo(Viagens::class, 'fk_id_viagem', 'pk_id_viagem');
    }
    
    /**
     * Calcula o custo total (combustível + pedágio)
     */
    public function getCustoTotalAttribute(): float
    {
        return ($this->custo_combustivel_estimado ?? 0) + ($this->pedagio_estimado ?? 0);
    }
    
    /**
     * Formata a duração em texto legível
     */
    public function getDuracaoTextoAttribute(): string
    {
        if (!$this->duracao_segundos) {
            return '-';
        }
        
        $horas = floor($this->duracao_segundos / 3600);
        $minutos = floor(($this->duracao_segundos % 3600) / 60);
        
        if ($horas > 0) {
            return sprintf('%dh %dmin', $horas, $minutos);
        }
        
        return sprintf('%dmin', $minutos);
    }
}
