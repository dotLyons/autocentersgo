<?php

namespace App\Livewire\Vehicles;

use App\Src\Vehicles\Models\Vehiculo;
use App\Src\Vehicles\Enums\TipoVehiculo;
use App\Src\Vehicles\Enums\TipoTransmision;
use App\Src\Vehicles\Enums\CategoriaPropiedad;
use App\Src\CRM\Models\Cliente;
use Livewire\Component;

class CreateEdit extends Component
{
    public $vehiculoId = null;

    // Fields
    public $marca, $modelo, $anio, $patente, $color, $tipo_vehiculo = 'auto';
    public $codigo_motor, $codigo_chasis_o_marco, $puertas, $tipo_caja = 'manual';
    public $version, $version_motor;
    public $tiene_gnc = false, $generacion_gnc;
    public $categoria_propiedad = 'propio';
    public $vendedor_id = null;
    public $precio_venta_publico = '';
    public $precio_venta_consignacion = '';
    public $ganancia_concesionaria = '';
    public $monto_entrega_requerido = '';

    // Búsqueda de vendedor si es consignación
    public $vendedores = [];

    public function mount($id = null)
    {
        $this->vendedores = Cliente::whereIn('tipo_cliente', ['vendedor', 'ambos'])->get();

        if ($id) {
            $vehiculo = Vehiculo::findOrFail($id);
            $this->vehiculoId = $vehiculo->id;
            $this->fill($vehiculo->toArray());
            
            // Cast Enums to strings for binding
            $this->tipo_vehiculo = $vehiculo->tipo_vehiculo->value;
            $this->tipo_caja = $vehiculo->tipo_caja?->value ?? 'manual';
            $this->categoria_propiedad = $vehiculo->categoria_propiedad->value;

            // Set specific fields from model
            $this->precio_venta_publico = $vehiculo->precio_venta_publico;
            $this->precio_venta_consignacion = $vehiculo->precio_venta_consignacion;
            $this->ganancia_concesionaria = $vehiculo->ganancia_concesionaria;
            $this->monto_entrega_requerido = $vehiculo->monto_entrega_requerido;
        }
    }

    protected function rules()
    {
        return [
            'marca' => 'required|string|max:255',
            'modelo' => 'required|string|max:255',
            'anio' => 'required|integer|min:1950|max:' . (date('Y') + 1),
            'patente' => 'nullable|string|max:20',
            'color' => 'nullable|string|max:100',
            'tipo_vehiculo' => 'required|in:auto,camioneta,furgon,moto',
            'codigo_motor' => 'nullable|string|max:255',
            'codigo_chasis_o_marco' => 'nullable|string|max:255',
            'puertas' => 'nullable|integer',
            'tipo_caja' => 'nullable|in:manual,automatica',
            'version' => 'nullable|string|max:255',
            'version_motor' => 'nullable|string|max:255',
            'tiene_gnc' => 'boolean',
            'generacion_gnc' => 'nullable|integer|min:1|max:5',
            'categoria_propiedad' => 'required|in:propio,consignacion',
            'vendedor_id' => 'required_if:categoria_propiedad,consignacion|nullable|exists:clientes,id',
            'precio_venta_publico' => 'nullable|numeric|min:0',
            'monto_entrega_requerido' => 'nullable|numeric|min:0',
            'precio_venta_consignacion' => 'required_if:categoria_propiedad,consignacion|nullable|numeric|min:0',
            'ganancia_concesionaria' => 'required_if:categoria_propiedad,consignacion|nullable|numeric|min:0',
        ];
    }

    public function updatedTieneGnc($value)
    {
        if (!$value) {
            $this->generacion_gnc = null;
        }
    }

    public function updatedCategoriaPropiedad($value)
    {
        if ($value === 'propio') {
            $this->vendedor_id = null;
            $this->precio_venta_consignacion = null;
            $this->ganancia_concesionaria = null;
        }
    }

    public function autoCalcularGanancia()
    {
        // Pequeño helper para el frontend
        if($this->precio_venta_publico > 0 && $this->precio_venta_consignacion > 0) {
            $this->ganancia_concesionaria = $this->precio_venta_publico - $this->precio_venta_consignacion;
        }
    }

    public function save()
    {
        $this->validate();

        Vehiculo::updateOrCreate(
            ['id' => $this->vehiculoId],
            [
                'marca' => $this->marca,
                'modelo' => $this->modelo,
                'anio' => $this->anio,
                'patente' => $this->patente,
                'color' => $this->color,
                'tipo_vehiculo' => $this->tipo_vehiculo,
                'codigo_motor' => $this->codigo_motor,
                'codigo_chasis_o_marco' => $this->codigo_chasis_o_marco,
                'puertas' => $this->tipo_vehiculo === 'moto' ? null : $this->puertas,
                'tipo_caja' => $this->tipo_vehiculo === 'moto' ? null : $this->tipo_caja,
                'version' => $this->version,
                'version_motor' => $this->version_motor,
                'tiene_gnc' => $this->tiene_gnc,
                'generacion_gnc' => $this->tiene_gnc ? $this->generacion_gnc : null,
                'categoria_propiedad' => $this->categoria_propiedad,
                'vendedor_id' => $this->categoria_propiedad === 'consignacion' ? $this->vendedor_id : null,
                'precio_venta_publico' => $this->precio_venta_publico ?: null,
                'precio_venta_consignacion' => $this->precio_venta_consignacion ?: null,
                'ganancia_concesionaria' => $this->ganancia_concesionaria ?: null,
                'monto_entrega_requerido' => $this->monto_entrega_requerido ?: null,
            ]
        );

        session()->flash('message', $this->vehiculoId ? 'Vehículo actualizado.' : 'Vehículo registrado al catálogo.');
        
        return $this->redirect(route('vehicles.index'), navigate: true);
    }

    public function render()
    {
        return view('livewire.vehicles.create-edit')->layout('layouts.app');
    }
}
