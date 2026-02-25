<?php

namespace App\Src\POS\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlanPagoTarjeta extends Model
{
    use HasFactory;

    protected $table = 'plan_pago_tarjetas';

    protected $fillable = [
        'tarjeta_id',
        'cuotas',
        'interes',
        'activo'
    ];

    protected $casts = [
        'cuotas' => 'integer',
        'interes' => 'decimal:2',
        'activo' => 'boolean'
    ];

    public function tarjeta(): BelongsTo
    {
        return $this->belongsTo(Tarjeta::class);
    }
}
