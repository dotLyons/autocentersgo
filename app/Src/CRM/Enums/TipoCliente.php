<?php

namespace App\Src\CRM\Enums;

enum TipoCliente: string
{
    case VENDEDOR = 'vendedor';
    case COMPRADOR = 'comprador';
    case AMBOS = 'ambos';
}
