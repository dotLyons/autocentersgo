<?php

namespace App\Src\POS\Models;

use App\Src\POS\Enums\EstadoCaja;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Caja extends Model
{
    use HasFactory;

    protected $table = 'cajas';

    protected $fillable = [
        'monto_apertura',
        'monto_cierre',
        'fecha_apertura',
        'fecha_cierre',
        'estado',
        'usuario_id',
    ];

    protected $casts = [
        'fecha_apertura' => 'datetime',
        'fecha_cierre' => 'datetime',
        'estado' => EstadoCaja::class,
        'monto_apertura' => 'decimal:2',
        'monto_cierre' => 'decimal:2',
    ];

    public function movimientos(): HasMany
    {
        return $this->hasMany(MovimientoCaja::class);
    }
}
