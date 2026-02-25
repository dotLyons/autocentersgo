<?php

namespace App\Src\POS\Enums;

enum EstadoCaja: string
{
    case ABIERTA = 'abierta';
    case CERRADA = 'cerrada';
}
