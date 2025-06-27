<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Viagens extends Model
{
    protected $primaryKey = 'pk_id_viagem'; 
    public $incrementing = true; 
    public $keyType = 'int';
    
    // Fazer o relacionamento com User
    public function user()
    {
        return $this->belongsTo(User::class, 'fk_id_usuario', 'id');
    }
}
