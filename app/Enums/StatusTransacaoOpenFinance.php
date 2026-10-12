<?php

namespace App\Enums;

/**
 * Situação da transação na nossa fila de conciliação (não no banco).
 */
enum StatusTransacaoOpenFinance: string
{
    case Pendente  = 'pendente';
    case Importada = 'importada';
    case Ignorada  = 'ignorada';
}
