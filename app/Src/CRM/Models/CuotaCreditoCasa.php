<?php

namespace App\Src\CRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CuotaCreditoCasa extends Model
{
    use HasFactory;

    protected $table = 'cuotas_credito_casa';

    protected $fillable = [
        'legajo_vehiculo_id',
        'numero_cuota',
        'monto',
        'interes_mora',
        'fecha_vencimiento',
        'pagada',
        'fecha_pago',
        'monto_pagado',
        'metodo_pago',
        'cobrado_por_id',
    ];

    protected $casts = [
        'numero_cuota' => 'integer',
        'monto' => 'decimal:2',
        'interes_mora' => 'decimal:2',
        'fecha_vencimiento' => 'date',
        'pagada' => 'boolean',
        'fecha_pago' => 'date',
        'monto_pagado' => 'decimal:2',
    ];

    public function legajoVehiculo(): BelongsTo
    {
        return $this->belongsTo(LegajoVehiculo::class);
    }
}
