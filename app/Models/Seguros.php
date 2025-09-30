<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Seguros extends Model
{
    use HasFactory;

    protected $table = 'seguros';
    protected $primaryKey = 'pk_id_seguro';
    public $incrementing = true;
    public $keyType = 'int';

    protected $fillable = [
        'fk_id_viagem',
        'seguradora',
        'plano',
        'detalhes_etarios',
        'link',
        'cobertura_medica',
        'cobertura_bagagem',
        'preco_pix',
        'preco_cartao',
        'parcelamento_cartao',
        'fk_id_viajante',
        'is_selected',
    ];

    // Se quiser acessar a viagem relacionada
    public function viagem()
    {
        return $this->belongsTo(Viagens::class, 'fk_id_viagem', 'pk_id_viagem');
    }

    // Relacionamento com viajante
    public function viajante()
    {
        return $this->belongsTo(Viajantes::class, 'fk_id_viajante', 'pk_id_viajante');
    }
}