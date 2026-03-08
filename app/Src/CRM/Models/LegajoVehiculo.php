<?php

namespace App\Src\CRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LegajoVehiculo extends Model
{
    use HasFactory;

    protected $table = 'legajo_vehiculos';

    protected $fillable = [
        'legajo_id',
        'vehiculo_id',
        
        // Vendedor specific fields
        'precio_acordado',
        'cargo_concesionaria',
        'esta_vendido',
        'metodo_pago_venta',
        
        // Comprador specific fields
        'precio_compra',
        'monto_efectivo',
        'monto_transferencia',
        'monto_entrega', // Otros tipos de entrega
        'vehiculo_entregado_id', // Si entregó un vehículo como forma de pago
        'valor_vehiculo_entregado', // En cuánto se tasó el vehículo entregado
        'financiacion_banco',
        'financiacion_casa',
        'cant_cuotas_casa',
        'monto_cuota_casa',
        'total_pagado_casa',
        'retirado_ahora',
        'costo_transferencia',
        
        // Saldo Pendiente de la Entrega Inicial
        'saldo_entrega_pendiente',
    ];

    protected $casts = [
        'precio_acordado' => 'decimal:2',
        'cargo_concesionaria' => 'decimal:2',
        'esta_vendido' => 'boolean',
        'precio_compra' => 'decimal:2',
        'monto_efectivo' => 'decimal:2',
        'monto_transferencia' => 'decimal:2',
        'monto_entrega' => 'decimal:2',
        'valor_vehiculo_entregado' => 'decimal:2',
        'financiacion_banco' => 'decimal:2',
        'financiacion_casa' => 'decimal:2',
        'monto_cuota_casa' => 'decimal:2',
        'total_pagado_casa' => 'decimal:2',
        'retirado_ahora' => 'boolean',
        'transferencia_a_cargo_comprador' => 'boolean',
        'costo_transferencia' => 'decimal:2',
        'saldo_entrega_pendiente' => 'decimal:2',
    ];

    public function legajo(): BelongsTo
    {
        return $this->belongsTo(Legajo::class);
    }

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(\App\Src\Vehicles\Models\Vehiculo::class, 'vehiculo_id');
    }

    public function vehiculoEntregado(): BelongsTo
    {
        return $this->belongsTo(\App\Src\Vehicles\Models\Vehiculo::class, 'vehiculo_entregado_id');
    }

    public function cuotasCreditoCasa(): HasMany
    {
        return $this->hasMany(CuotaCreditoCasa::class);
    }

    public function cuotasCasa(): HasMany
    {
        return $this->hasMany(CuotaCreditoCasa::class);
    }

    public function pagosEntrega(): HasMany
    {
        return $this->hasMany(PagoEntrega::class);
    }
}
