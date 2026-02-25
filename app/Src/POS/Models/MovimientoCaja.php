<?php

namespace App\Src\POS\Models;

use App\Src\POS\Enums\TipoMetodoPago;
use App\Src\POS\Enums\TipoMovimiento;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MovimientoCaja extends Model
{
    use HasFactory;

    protected $table = 'movimientos_caja';

    protected $fillable = [
        'caja_id',
        'tipo_movimiento',
        'monto',
        'descripcion',
        'metodo_pago',
        'tarjeta_id',
        'plan_pago_tarjeta_id',
        'referencia_transferencia', // If transfer
    ];

    protected $casts = [
        'tipo_movimiento' => TipoMovimiento::class,
        'metodo_pago' => TipoMetodoPago::class,
        'monto' => 'decimal:2',
    ];

    public function caja(): BelongsTo
    {
        return $this->belongsTo(Caja::class);
    }

    public function tarjeta(): BelongsTo
    {
        return $this->belongsTo(Tarjeta::class);
    }

    public function planPago(): BelongsTo
    {
        return $this->belongsTo(PlanPagoTarjeta::class, 'plan_pago_tarjeta_id');
    }
}
