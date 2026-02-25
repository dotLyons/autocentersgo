<?php

namespace App\Src\CRM\Models;

use App\Src\CRM\Enums\TipoLegajo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Legajo extends Model
{
    use HasFactory;

    protected $table = 'legajos';

    protected $fillable = [
        'cliente_id',
        'tipo_legajo',
    ];

    protected $casts = [
        'tipo_legajo' => TipoLegajo::class,
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class);
    }

    public function vehiculos(): HasMany
    {
        return $this->hasMany(LegajoVehiculo::class);
    }
}
