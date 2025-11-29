-- Script para limpar viagens incompletas criadas devido ao erro
-- Execute este script para remover as viagens ID 2 e 3 que foram criadas sem dados completos

-- Como as tabelas têm ON DELETE CASCADE, ao deletar a viagem, 
-- todos os registros relacionados também serão deletados

DELETE FROM viagens WHERE pk_id_viagem IN (2, 3);

-- Confirme que foram deletadas
SELECT COUNT(*) as total_viagens FROM viagens;
