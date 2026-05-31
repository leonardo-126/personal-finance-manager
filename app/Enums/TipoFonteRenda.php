<?php
enum TipoFonteRenda: string
{
    case Salario      = 'salário';
    case Investimento = 'investimento';
    case Extra        = 'extra';
    case Outro        = 'outro';
}