<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Viagens extends Model
{
    protected $primaryKey = 'pk_id_viagem'; 
    public $incrementing = true; 
    public $keyType = 'int';
    
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
}
