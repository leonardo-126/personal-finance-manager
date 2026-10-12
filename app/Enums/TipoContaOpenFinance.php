<?php

namespace App\Enums;

enum TipoContaOpenFinance: string
{
    case Corrente = 'corrente';
    case Poupanca = 'poupanca';
    case Cartao   = 'cartao';
    case Outra    = 'outra';
}
