<?php

namespace App\Src\POS\Models;

use App\Src\POS\Enums\TipoTarjeta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tarjeta extends Model
{
    use HasFactory;

    protected $table = 'tarjetas';

    protected $fillable = [
        'nombre', // Visa, MasterCard
        'tipo', // debito, credito
        'activa'
    ];

    protected $casts = [
        'tipo' => TipoTarjeta::class,
        'activa' => 'boolean'
    ];

    public function planesDePago(): HasMany
    {
        return $this->hasMany(PlanPagoTarjeta::class);
    }
}
