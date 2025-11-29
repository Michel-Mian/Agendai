<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\AiChatMessage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CleanOldChatMessages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'chat:clean {--days=30 : Number of days to keep messages}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove mensagens de chat com IA mais antigas que X dias';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $cutoffDate = Carbon::now()->subDays($days);

        $this->info("Limpando mensagens anteriores a {$cutoffDate->format('d/m/Y H:i:s')}...");

        $count = AiChatMessage::where('created_at', '<', $cutoffDate)->count();

        if ($count === 0) {
            $this->info('Nenhuma mensagem antiga para limpar.');
            return 0;
        }

        $this->warn("Encontradas {$count} mensagens para deletar.");

        if ($this->confirm('Deseja continuar?', true)) {
            $deleted = AiChatMessage::where('created_at', '<', $cutoffDate)->delete();
            $this->info("✅ {$deleted} mensagens foram deletadas com sucesso!");
            
            // Otimizar tabela
            $this->info('Otimizando tabela...');
            DB::statement('OPTIMIZE TABLE ai_chat_messages');
            $this->info('✅ Tabela otimizada!');
        } else {
            $this->warn('Operação cancelada.');
        }

        return 0;
    }
}
