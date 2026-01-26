<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vehiculo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "vehiculos";

    protected $fillable = [
        'brand', 'model', 'patent', 'model_year',
        'color', 'price', 'details', 'is_available',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'model_year' => 'integer',
            'is_available' => 'boolean',
        ];
    }
}
