<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seguros extends Model
{
    // Nome da tabela (caso não seja o padrão)
    protected $table = 'seguros';

    // Chave primária personalizada
    protected $primaryKey = 'pk_id_seguro';

    // Permitir atribuição em massa
    protected $fillable = [
        'site',
        'preco',
        'preco_pix',
        'preco_cartao',
        'parcelas',
        'dados',
        'link',
        'fk_id_viagem',
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
    ];

    // Se quiser acessar a viagem relacionada
    public function viagem()
    {
        return $this->belongsTo(Viagens::class, 'fk_id_viagem', 'pk_id_viagem');
    }
}
