<?php

namespace App\Enums;

enum StatusConexaoOpenFinance: string
{
    case Atualizando       = 'atualizando';
    case Ativa             = 'ativa';
    case ErroLogin         = 'erro_login';
    case Desatualizada     = 'desatualizada';
    case AguardandoUsuario = 'aguardando_usuario';

    /** Só faz sentido buscar contas/transações quando o agregador terminou de atualizar com sucesso. */
    public function permiteSincronizar(): bool
    {
        return $this === self::Ativa;
    }
}
