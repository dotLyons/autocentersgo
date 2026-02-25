<?php

namespace App\Src\POS\Enums;

enum TipoMetodoPago: string
{
    case EFECTIVO = 'efectivo';
    case TRANSFERENCIA = 'transferencia';
    case TARJETA = 'tarjeta';
}
