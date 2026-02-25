<?php

namespace App\Src\CRM\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PagoEntrega extends Model
{
    use HasFactory;

    protected $table = 'pagos_entregas';

    protected $fillable = [
        'legajo_vehiculo_id',
        'monto',
        'fecha_pago',
        'metodo_pago',
        'movimiento_caja_id',
        'registrado_por',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'fecha_pago' => 'date',
    ];

    public function legajoVehiculo(): BelongsTo
    {
        return $this->belongsTo(LegajoVehiculo::class);
    }
}
