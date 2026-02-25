<?php

namespace App\Src\Vehicles\Enums;

enum TipoMantenimiento: string
{
    case TALLER = 'taller';
    case LAVADERO = 'lavadero';
    case OTRO = 'otro';
}
