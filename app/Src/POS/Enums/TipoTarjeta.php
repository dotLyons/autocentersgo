<?php

namespace App\Src\POS\Enums;

enum TipoTarjeta: string
{
    case DEBITO = 'debito';
    case CREDITO = 'credito';
}
