<?php

namespace App\Src\POS\Enums;

enum TipoMovimiento: string
{
    case INGRESO = 'ingreso';
    case EGRESO = 'egreso';
}
