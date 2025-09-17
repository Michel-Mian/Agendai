<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Destinos extends Model
{
    use HasFactory;

    protected $table = 'destinos';
    protected $primaryKey = 'pk_id_destino';
    protected $fillable = [
        'nome_destino',
        'data_chegada_destino',
        'data_partida_destino',
        'ordem_destino',
        'fk_id_viagem',
    ];

    public function viagem(): BelongsTo
    {
        return $this->belongsTo(Viagens::class, 'fk_id_viagem', 'pk_id_viagem');
    }
}