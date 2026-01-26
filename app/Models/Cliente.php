<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    protected $table = "clientes";

    protected $fillable = [
        'dni', 'nombre', 'apellido', 'telefono',
        'direccion', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }
}
