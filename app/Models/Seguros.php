<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seguros extends Model
{
    protected $table = 'seguros';
    protected $primaryKey = 'pk_id_seguro';

    protected $fillable = [
        'site',
        'titulo',
        'link',
        'cobertura_medica',
        'cobertura_bagagem',
        'cobertura_cancelamento',
        'cobertura_odonto',
        'cobertura_medicamentos',
        'cobertura_eletronicos',
        'cobertura_mochila_mao',
        'cobertura_atraso_embarque',
        'cobertura_pet',
        'cobertura_sala_vip',
        'cobertura_telemedicina',
        'preco_pix',
        'preco_cartao',
        'parcelas',
        'preco',
        'fk_id_viagem',
    ];

    protected $casts = [
        'titulo' => 'array',
        'cobertura_telemedicina' => 'boolean',
        'preco_pix' => 'string',
        'preco_cartao' => 'string',
        'preco' => 'string',
    ];

    public function viagem()
    {
        return $this->belongsTo(Viagens::class, 'fk_id_viagem', 'pk_id_viagem');
    }
}