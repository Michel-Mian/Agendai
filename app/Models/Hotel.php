<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Viagens;


class Hotel extends Model
{
    use HasFactory;
    
    protected $table = 'hotels';
    protected $primaryKey = 'pk_id_hotel';

    protected $fillable = [
        'nome_hotel',
        'latitude',
        'longitude',
        'avaliacao',
        'data_check_in',
        'data_check_out',
        'preco',
        'image_url',
        'fk_id_viagem',
    ];

    /**
     * Relacionamento com a tabela de viagens.
     */
    public function viagens()
    {
        return $this->belongsTo(Viagens::class, 'fk_id_viagem', 'pk_id_viagem');
    }
}
