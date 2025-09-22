<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Viagens extends Model
{
    protected $primaryKey = 'pk_id_viagem'; 
    public $incrementing = true; 
    public $keyType = 'int';
    
    // Campos que podem ser preenchidos via mass assignment
    protected $fillable = [
        'destino_viagem',
        'origem_viagem', 
        'data_inicio_viagem',
        'data_final_viagem',
        'orcamento_viagem',
        'fk_id_usuario',
        'fk_id_seguro_selecionado'
    ];
    
    // Relação com  usuário (User)
    public function user()
    {
        return $this->belongsTo(User::class, 'fk_id_usuario', 'id');
    }

    // Relação com os pontos de interesse (PontoInteresse)
    public function pontosInteresse()
    {
        return $this->hasMany(PontoInteresse::class, 'fk_id_viagem', 'pk_id_viagem');
    }

    // Relação com os objetivos (Objetivos)
    public function objetivos()
    {
        return $this->hasMany(Objetivos::class, 'fk_id_viagem', 'pk_id_viagem');
    }

    // Relação com os viajantes (Viajantes)
    public function viajantes()
    {
        return $this->hasMany(Viajantes::class, 'fk_id_viagem', 'pk_id_viagem');
    }

    // Relação com os voos (Voos)
    public function voos()
    {
        return $this->hasMany(Voos::class, 'fk_id_viagem', 'pk_id_viagem');
    }

    public function hotel()
    {
        return $this->hasMany(Hotel::class, 'fk_id_viagem', 'pk_id_viagem');
    }

    // Relação com os seguros (Seguros)
    public function seguros()
    {
        return $this->hasMany(Seguros::class, 'fk_id_viagem', 'pk_id_viagem');
    }

    // Relação com o seguro selecionado
    public function seguroSelecionado()
    {
        return $this->belongsTo(Seguros::class, 'fk_id_seguro_selecionado', 'pk_id_seguro');
    }

    public function destinos(): HasMany
    {
        return $this->hasMany(Destinos::class, 'fk_id_viagem', 'pk_id_viagem')->orderBy('ordem_destino', 'asc');
    }
}
