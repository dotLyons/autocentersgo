<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cobro extends Model
{
    protected $guarded = [];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function detalles()
    {
        return $this->hasMany(CobroDetalle::class);
    }

    // Un cobro genera un movimiento de caja
    public function movimientoCaja()
    {
        return $this->morphOne(CajaMovimiento::class, 'origen');
    }
}
