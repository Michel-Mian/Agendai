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
        'dados',
        'link',
        'fk_id_viagem',
    ];

    // Se quiser acessar a viagem relacionada
    public function viagem()
    {
        return $this->belongsTo(Viagens::class, 'fk_id_viagem', 'pk_id_viagem');
    }
}
