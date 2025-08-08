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
    // REMOVIDO: public $incrementing = false; // Agora será auto-incrementável
    // REMOVIDO: protected $keyType = 'string'; // Agora será um inteiro

    protected $fillable = [
        // REMOVIDO: 'pk_id_hotel', // Não é mais fillable, pois é auto-incrementável
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
    public function viagem()
    {
        return $this->belongsTo(Viagens::class, 'fk_id_viagem', 'pk_id_viagem');
    }
}
