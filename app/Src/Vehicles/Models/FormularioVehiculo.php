<?php

namespace App\Src\Vehicles\Models;

use App\Src\Vehicles\Enums\TipoFormulario;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FormularioVehiculo extends Model
{
    use HasFactory;

    protected $table = 'formularios_vehiculo';

    protected $fillable = [
        'vehiculo_id',
        'tipo_formulario',
        'presentado',
        'archivo_path',
        'fecha_presentacion',
        'observaciones',
    ];

    protected $casts = [
        'tipo_formulario' => TipoFormulario::class,
        'presentado' => 'boolean',
        'fecha_presentacion' => 'date',
    ];

    public function vehiculo(): BelongsTo
    {
        return $this->belongsTo(Vehiculo::class);
    }
}
