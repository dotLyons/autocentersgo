<?php

namespace App\Src\Vehicles\Models;

use App\Src\Vehicles\Enums\TipoMantenimiento;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MantenimientoVehiculo extends Model
{
    use HasFactory;

    protected $table = 'mantenimientos_vehiculo';

    protected $fillable = [
        'vehiculo_id',
        'tipo_mantenimiento',
        'descripcion_tareas',
        'piezas_cambiadas',
        'nombre_lugar',
        'direccion_lugar',
        'monto',
        'fecha_llevado',
        'fecha_devuelto',
        'responsable_llevado',
    ];

    protected $casts = [
        'tipo_mantenimiento' => TipoMantenimiento::class,
        'monto' => 'decimal:2',
        'fecha_llevado' => 'datetime',
        'fecha_devuelto' => 'datetime',
    ];

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }
}
