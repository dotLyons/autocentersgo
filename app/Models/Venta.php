<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'ventas';

    protected $fillable = [
        'cliente_id', 'vehiculo_id', 'precio_venta',
        'cantidad_cuotas', 'monto_cuota', 'observaciones',
        'fecha_cobro'
    ];

    protected function casts(): array
    {
        return [
            'fecha_cobro' => 'date'
        ];
    }

    // Relación con Cliente
    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }

    // Relación con Vehículo
    public function vehiculo()
    {
        return $this->belongsTo(Vehiculo::class);
    }
}
