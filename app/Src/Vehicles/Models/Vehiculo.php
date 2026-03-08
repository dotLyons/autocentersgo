<?php

namespace App\Src\Vehicles\Models;

use App\Src\Vehicles\Enums\CategoriaPropiedad;
use App\Src\Vehicles\Enums\TipoTransmision;
use App\Src\Vehicles\Enums\TipoVehiculo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehiculo extends Model
{
    use HasFactory;

    protected $table = 'vehiculos';

    protected $fillable = [
        'marca',
        'modelo',
        'anio',
        'patente',
        'color',
        'tipo_vehiculo',
        'codigo_motor', // For multiple types (motor / motor version)
        'codigo_chasis_o_marco', // Chassis for auto/pickup/furgon, Marco for moto
        'puertas',
        'tipo_caja',
        'version',
        'version_motor',
        'tiene_gnc',
        'generacion_gnc',
        'categoria_propiedad',
        'vendedor_id', // foreign to clientes, if consignacion
        'precio_venta_publico', // For stock
        'precio_venta_consignacion', // Agregado para vendedor
        'ganancia_concesionaria', // Agregado para vendedor
        'monto_entrega_requerido', // Minimum delivery required
        'disponible',
    ];

    protected $casts = [
        'tipo_vehiculo' => TipoVehiculo::class,
        'tipo_caja' => TipoTransmision::class,
        'categoria_propiedad' => CategoriaPropiedad::class,
        'tiene_gnc' => 'boolean',
        'precio_venta_publico' => 'decimal:2',
        'precio_venta_consignacion' => 'decimal:2',
        'ganancia_concesionaria' => 'decimal:2',
        'monto_entrega_requerido' => 'decimal:2',
        'disponible' => 'boolean',
    ];

    public function mantenimientos(): HasMany
    {
        return $this->hasMany(MantenimientoVehiculo::class);
    }

    public function formularios(): HasMany
    {
        return $this->hasMany(FormularioVehiculo::class);
    }
}
