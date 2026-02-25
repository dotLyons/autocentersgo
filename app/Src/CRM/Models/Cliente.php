<?php

namespace App\Src\CRM\Models;

use App\Src\CRM\Enums\TipoCliente;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
        'dni',
        'nombre',
        'apellido',
        'celular',
        'celular_referencia',
        'email',
        'tipo_cliente',
    ];

    protected $casts = [
        'tipo_cliente' => TipoCliente::class,
    ];

    public function legajos(): HasMany
    {
        return $this->hasMany(Legajo::class);
    }
}
