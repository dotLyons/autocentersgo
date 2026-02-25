<?php

namespace App\Livewire\Vehicles;

use App\Src\Vehicles\Models\Vehiculo;
use App\Src\Vehicles\Models\MantenimientoVehiculo;
use App\Src\Vehicles\Models\FormularioVehiculo;
use App\Src\CRM\Models\Cliente;
use Livewire\Component;
use Livewire\WithFileUploads;

class Show extends Component
{
    use WithFileUploads;

    public Vehiculo $vehiculo;
    public $vendedor = null;

    // Modals
    public $isOpenMantenimiento = false;
    public $isOpenFormulario = false;

    // Mantenimiento Fields
    public $mant_tipo = 'taller';
    public $mant_descripcion = '';
    public $mant_lugar = '';
    public $mant_monto = '';
    public $mant_fecha = '';
    public $mant_repuestos = '';
    public $mant_responsable = '';

    // Formulario Fields
    public $form_tipo = 'Formulario 08';
    public $form_presentado = true;
    public $form_archivo = null;
    public $form_fecha = '';
    public $form_obs = '';

    public function mount($id)
    {
        $this->vehiculo = Vehiculo::with(['mantenimientos', 'formularios'])->findOrFail($id);
        
        if ($this->vehiculo->categoria_propiedad->value === 'consignacion' && $this->vehiculo->vendedor_id) {
            $this->vendedor = Cliente::find($this->vehiculo->vendedor_id);
        }
    }

    public function abrirModalMantenimiento()
    {
        $this->mant_tipo = 'taller';
        $this->mant_descripcion = '';
        $this->mant_lugar = '';
        $this->mant_monto = '';
        $this->mant_fecha = now()->format('Y-m-d');
        $this->mant_repuestos = '';
        // If there's an authenticated user we can use the name
        $this->mant_responsable = auth()->user() ? auth()->user()->name : 'Taller Propio'; 
        
        $this->isOpenMantenimiento = true;
    }

    public function guardarMantenimiento()
    {
        $this->validate([
            'mant_tipo' => 'required|in:taller,lavadero,otro',
            'mant_descripcion' => 'required|string|max:255',
            'mant_lugar' => 'required|string|max:255',
            'mant_monto' => 'nullable|numeric|min:0',
            'mant_fecha' => 'required|date',
            'mant_repuestos' => 'nullable|string|max:255',
            'mant_responsable' => 'required|string|max:100',
        ]);

        MantenimientoVehiculo::create([
            'vehiculo_id' => $this->vehiculo->id,
            'tipo_mantenimiento' => $this->mant_tipo,
            'descripcion_tareas' => $this->mant_descripcion,
            'piezas_cambiadas' => $this->mant_repuestos,
            'nombre_lugar' => $this->mant_lugar,
            'monto' => $this->mant_monto ?: null,
            'fecha_llevado' => $this->mant_fecha,
            'responsable_llevado' => $this->mant_responsable,
        ]);

        session()->flash('message', 'Mantenimiento registrado con éxito.');
        $this->isOpenMantenimiento = false;
        $this->vehiculo->refresh();
    }

    public function abrirModalFormulario()
    {
        $this->form_tipo = 'Formulario 08';
        $this->form_presentado = true;
        $this->form_archivo = null;
        $this->form_fecha = now()->format('Y-m-d');
        $this->form_obs = '';
        $this->isOpenFormulario = true;
    }

    public function guardarFormulario()
    {
        $this->validate([
            'form_tipo' => 'required|string',
            'form_presentado' => 'boolean',
            'form_archivo' => 'nullable|file|max:10240', // Max 10MB
            'form_fecha' => 'required|date',
            'form_obs' => 'nullable|string|max:255',
        ]);

        $path = null;
        if ($this->form_archivo) {
            $path = $this->form_archivo->store('formularios/vehiculos', 'public');
        }

        // Check if there is already a record for this form type, to update it
        $formCheck = FormularioVehiculo::where('vehiculo_id', $this->vehiculo->id)
            ->where('tipo_formulario', $this->form_tipo)
            ->first();

        if ($formCheck) {
            $formCheck->update([
                'presentado' => $this->form_presentado,
                'archivo_path' => $path ? $path : $formCheck->archivo_path,
                'fecha_presentacion' => $this->form_fecha,
                'observaciones' => $this->form_obs,
            ]);
        } else {
            FormularioVehiculo::create([
                'vehiculo_id' => $this->vehiculo->id,
                'tipo_formulario' => $this->form_tipo,
                'presentado' => $this->form_presentado,
                'archivo_path' => $path,
                'fecha_presentacion' => $this->form_fecha,
                'observaciones' => $this->form_obs,
            ]);
        }

        session()->flash('message', 'Formulario adjuntado correctamente.');
        $this->isOpenFormulario = false;
        $this->vehiculo->refresh();
    }

    public function render()
    {
        return view('livewire.vehicles.show')->layout('layouts.app');
    }
}
