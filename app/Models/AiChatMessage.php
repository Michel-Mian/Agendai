<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiChatMessage extends Model
{
    protected $fillable = [
        'viagem_id',
        'user_id',
        'role',
        'content'
    ];

    /**
     * Relacionamento com Viagem
     */
    public function viagem(): BelongsTo
    {
        return $this->belongsTo(Viagens::class, 'viagem_id', 'pk_id_viagem');
    }

    /**
     * Relacionamento com User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
