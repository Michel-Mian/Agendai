<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Carbon\Carbon;

class Viagens extends Model
{
    protected $primaryKey = 'pk_id_viagem'; 
    public $incrementing = true; 
    public $keyType = 'int';
    
    // Campos que podem ser preenchidos via mass assignment
    protected $fillable = [
        'nome_viagem',
        'origem_viagem', 
        'data_inicio_viagem',
        'data_final_viagem',
        'orcamento_viagem',
        'fk_id_usuario'
    ];
    
    protected $casts = [
        'data_inicio_viagem' => 'date',
        'data_final_viagem' => 'date'
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

    // Relação com os seguros (Seguros) - através dos viajantes
    // Agora retorna TODOS os seguros ligados diretamente à viagem via fk_id_viagem
    public function seguros(): HasMany
    {
        return $this->hasMany(Seguros::class, 'fk_id_viagem', 'pk_id_viagem');
    }

    public function destinos(): HasMany
    {
        return $this->hasMany(Destinos::class, 'fk_id_viagem', 'pk_id_viagem')->orderBy('ordem_destino', 'asc');
    }

    // Relação com os veículos (Veiculos)
    public function veiculos(): HasMany
    {
        return $this->hasMany(Veiculos::class, 'fk_id_viagem', 'pk_id_viagem');
    }

    // Relacionamento para obter o seguro marcado como selecionado para a viagem
    public function seguroSelecionado(): HasOne
    {
        // Usa a relação direta pela chave fk_id_viagem e filtra por is_selected
        return $this->hasOne(Seguros::class, 'fk_id_viagem', 'pk_id_viagem')
                    ->where('is_selected', true);
    }

    // Relacionamento para obter o veículo marcado como selecionado para a viagem
    public function veiculoSelecionado(): HasOne
    {
        return $this->hasOne(Veiculos::class, 'fk_id_viagem', 'pk_id_viagem')
                    ->where('is_selected', true)
                    ->latest('pk_id_veiculo');
    }

    // Relacionamento com dados de viagem de carro próprio (1:1)
    public function viagemCarro(): HasOne
    {
        return $this->hasOne(ViagemCarro::class, 'fk_id_viagem', 'pk_id_viagem');
    }

    // Atributo calculado: status da viagem (concluida, andamento, proxima)
    public function getStatusAttribute(): string
    {
        $hoje = Carbon::today();
        $inicio = Carbon::parse($this->data_inicio_viagem);
        $fim = Carbon::parse($this->data_final_viagem);

        if ($fim->lt($hoje)) {
            return 'concluida';
        }
        if ($inicio->gt($hoje)) {
            return 'planejada';
        }
        return 'andamento';
    }
}
