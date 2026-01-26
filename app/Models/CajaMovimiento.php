<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CajaMovimiento extends Model
{
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación polimórfica (por si el movimiento viene de un Cobro)
    public function origen()
    {
        return $this->morphTo();
    }
}
