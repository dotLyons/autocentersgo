<?php

namespace Database\Seeders;

use App\Src\Vehicles\Models\Vehiculo;
use App\Src\Vehicles\Models\MantenimientoVehiculo;
use App\Src\Vehicles\Models\FormularioVehiculo;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class VehiclesContextSeeder extends Seeder
{
    /**
     * Seed the Vehicles context with test data
     */
    public function run(): void
    {
        // ===== VEHÍCULOS =====
        // Auto 1: Sedan moderno en consignación
        $vehiculo1 = Vehiculo::create([
            'marca' => 'Toyota',
            'modelo' => 'Corolla',
            'anio' => 2022,
            'patente' => 'ABC123',
            'color' => 'Blanco',
            'tipo_vehiculo' => 'auto',
            'codigo_motor' => 'MOT-2022-001',
            'codigo_chasis_o_marco' => 'CHASIS-2022-001',
            'puertas' => 4,
            'tipo_caja' => 'automatica',
            'version' => '1.6 XLi',
            'version_motor' => '1.6L',
            'tiene_gnc' => false,
            'generacion_gnc' => null,
            'categoria_propiedad' => 'consignacion',
            'vendedor_id' => 1, // Cliente 1
            'precio_venta_publico' => 1850000.00,
            'precio_venta_consignacion' => 1700000.00,
            'ganancia_concesionaria' => 150000.00,
        ]);

        // Auto 2: Sedan usado con GNC
        $vehiculo2 = Vehiculo::create([
            'marca' => 'Volkswagen',
            'modelo' => 'Vento',
            'anio' => 2018,
            'patente' => 'DEF456',
            'color' => 'Gris',
            'tipo_vehiculo' => 'auto',
            'codigo_motor' => 'MOT-2018-002',
            'codigo_chasis_o_marco' => 'CHASIS-2018-002',
            'puertas' => 4,
            'tipo_caja' => 'manual',
            'version' => '1.6 Confortline',
            'version_motor' => '1.6L',
            'tiene_gnc' => true,
            'generacion_gnc' => 5,
            'categoria_propiedad' => 'propio',
            'vendedor_id' => null,
            'precio_venta_publico' => 1200000.00,
            'precio_venta_consignacion' => null,
            'ganancia_concesionaria' => 0.00,
        ]);

        // Camioneta 3: Pickup grande
        $vehiculo3 = Vehiculo::create([
            'marca' => 'Ford',
            'modelo' => 'F-100',
            'anio' => 2015,
            'patente' => 'GHI789',
            'color' => 'Rojo',
            'tipo_vehiculo' => 'camioneta',
            'codigo_motor' => 'MOT-2015-003',
            'codigo_chasis_o_marco' => 'CHASIS-2015-003',
            'puertas' => 2,
            'tipo_caja' => 'manual',
            'version' => 'XL',
            'version_motor' => '4.0L',
            'tiene_gnc' => false,
            'generacion_gnc' => null,
            'categoria_propiedad' => 'consignacion',
            'vendedor_id' => 3, // Cliente 3
            'precio_venta_publico' => 1600000.00,
            'precio_venta_consignacion' => 1450000.00,
            'ganancia_concesionaria' => 150000.00,
        ]);

        // Moto 4: Motoneta
        $vehiculo4 = Vehiculo::create([
            'marca' => 'Honda',
            'modelo' => 'CG 150',
            'anio' => 2021,
            'patente' => 'JKL012',
            'color' => 'Negro',
            'tipo_vehiculo' => 'moto',
            'codigo_motor' => 'MOT-2021-004',
            'codigo_chasis_o_marco' => 'MARCO-2021-004',
            'puertas' => null,
            'tipo_caja' => 'manual',
            'version' => 'CG 150',
            'version_motor' => '150cc',
            'tiene_gnc' => false,
            'generacion_gnc' => null,
            'categoria_propiedad' => 'propio',
            'vendedor_id' => null,
            'precio_venta_publico' => 280000.00,
            'precio_venta_consignacion' => null,
            'ganancia_concesionaria' => 0.00,
        ]);

        // Auto 5: SUV compacta
        $vehiculo5 = Vehiculo::create([
            'marca' => 'Chevrolet',
            'modelo' => 'Tracker',
            'anio' => 2020,
            'patente' => 'MNO345',
            'color' => 'Azul',
            'tipo_vehiculo' => 'auto',
            'codigo_motor' => 'MOT-2020-005',
            'codigo_chasis_o_marco' => 'CHASIS-2020-005',
            'puertas' => 4,
            'tipo_caja' => 'automatica',
            'version' => '1.8 LTZ',
            'version_motor' => '1.8L',
            'tiene_gnc' => true,
            'generacion_gnc' => 5,
            'categoria_propiedad' => 'propio',
            'vendedor_id' => null,
            'precio_venta_publico' => 1950000.00,
            'precio_venta_consignacion' => null,
            'ganancia_concesionaria' => 0.00,
        ]);

        // Furgón 6: Pequeño comercial
        $vehiculo6 = Vehiculo::create([
            'marca' => 'Renault',
            'modelo' => 'Kangoo',
            'anio' => 2019,
            'patente' => 'PQR678',
            'color' => 'Blanco',
            'tipo_vehiculo' => 'furgon',
            'codigo_motor' => 'MOT-2019-006',
            'codigo_chasis_o_marco' => 'CHASIS-2019-006',
            'puertas' => 3,
            'tipo_caja' => 'manual',
            'version' => '1.6 Access',
            'version_motor' => '1.6L',
            'tiene_gnc' => true,
            'generacion_gnc' => 4,
            'categoria_propiedad' => 'propio',
            'vendedor_id' => null,
            'precio_venta_publico' => 980000.00,
            'precio_venta_consignacion' => null,
            'ganancia_concesionaria' => 0.00,
        ]);

        // ===== MANTENIMIENTOS =====
        // Mantenimiento 1: Corolla - Taller de revisión general
        MantenimientoVehiculo::create([
            'vehiculo_id' => $vehiculo1->id,
            'tipo_mantenimiento' => 'taller',
            'descripcion_tareas' => 'Revisión general, cambio de aceite, filtro de aire',
            'piezas_cambiadas' => 'Aceite 5L Mobil 1, Filtro Aire OEM',
            'nombre_lugar' => 'Taller Profesional "El Mecánico"',
            'direccion_lugar' => 'Av. Principal 1234, Centro',
            'monto' => 3500.00,
            'fecha_llevado' => Carbon::now()->subDays(30),
            'fecha_devuelto' => Carbon::now()->subDays(28),
            'responsable_llevado' => 'Juan Pérez',
        ]);

        // Mantenimiento 2: Vento - Lavadero
        MantenimientoVehiculo::create([
            'vehiculo_id' => $vehiculo2->id,
            'tipo_mantenimiento' => 'lavadero',
            'descripcion_tareas' => 'Lavado completo exterior e interior',
            'piezas_cambiadas' => null,
            'nombre_lugar' => 'Lavadero "Brillo Total"',
            'direccion_lugar' => 'Calle 9 de Julio 567',
            'monto' => 800.00,
            'fecha_llevado' => Carbon::now()->subDays(5),
            'fecha_devuelto' => Carbon::now()->subDays(5),
            'responsable_llevado' => 'Carlos López',
        ]);

        // Mantenimiento 3: F-100 - Taller especializado
        MantenimientoVehiculo::create([
            'vehiculo_id' => $vehiculo3->id,
            'tipo_mantenimiento' => 'taller',
            'descripcion_tareas' => 'Reparación tren delantero, cambio de pastillas de frenos',
            'piezas_cambiadas' => 'Pastillas frenos, disco freno trasero',
            'nombre_lugar' => 'Taller de Camionetas "Ruedas Fuertes"',
            'direccion_lugar' => 'Barrio Industrial, Zona Sur',
            'monto' => 8500.00,
            'fecha_llevado' => Carbon::now()->subDays(15),
            'fecha_devuelto' => Carbon::now()->subDays(10),
            'responsable_llevado' => 'Carlos López',
        ]);

        // Mantenimiento 4: CG 150 - Taller de motos
        MantenimientoVehiculo::create([
            'vehiculo_id' => $vehiculo4->id,
            'tipo_mantenimiento' => 'taller',
            'descripcion_tareas' => 'Cambio de cadena, ajuste de frenos',
            'piezas_cambiadas' => 'Cadena 420, pastillas frenos',
            'nombre_lugar' => 'Taller de Motos "Dos Ruedas"',
            'direccion_lugar' => 'Pasaje Moto 123, Barrio Norte',
            'monto' => 1200.00,
            'fecha_llevado' => Carbon::now()->subDays(20),
            'fecha_devuelto' => Carbon::now()->subDays(19),
            'responsable_llevado' => 'Admin',
        ]);

        // Mantenimiento 5: Tracker - Lavadero
        MantenimientoVehiculo::create([
            'vehiculo_id' => $vehiculo5->id,
            'tipo_mantenimiento' => 'lavadero',
            'descripcion_tareas' => 'Lavado completo, encerado',
            'piezas_cambiadas' => null,
            'nombre_lugar' => 'Lavadero "Brillo Total"',
            'direccion_lugar' => 'Calle 9 de Julio 567',
            'monto' => 1200.00,
            'fecha_llevado' => Carbon::now()->subDays(8),
            'fecha_devuelto' => Carbon::now()->subDays(7),
            'responsable_llevado' => 'Juan Pérez',
        ]);

        // ===== FORMULARIOS VEHÍCULOS =====
        // Formularios para Toyota Corolla
        FormularioVehiculo::create([
            'vehiculo_id' => $vehiculo1->id,
            'tipo_formulario' => 'Formulario 02',
            'presentado' => true,
            'archivo_path' => '/documentos/vehiculos/ABC123_form02.pdf',
            'fecha_presentacion' => Carbon::now()->subDays(25),
            'observaciones' => 'Documento presentado sin inconvenientes',
        ]);

        FormularioVehiculo::create([
            'vehiculo_id' => $vehiculo1->id,
            'tipo_formulario' => 'Formulario 04',
            'presentado' => true,
            'archivo_path' => '/documentos/vehiculos/ABC123_form04.pdf',
            'fecha_presentacion' => Carbon::now()->subDays(25),
            'observaciones' => 'Transferencia en trámite',
        ]);

        // Formularios para Volkswagen Vento
        FormularioVehiculo::create([
            'vehiculo_id' => $vehiculo2->id,
            'tipo_formulario' => 'Libre de Deuda',
            'presentado' => true,
            'archivo_path' => '/documentos/vehiculos/DEF456_libredeuda.pdf',
            'fecha_presentacion' => Carbon::now()->subDays(10),
            'observaciones' => 'Regularizado ante afip',
        ]);

        // Formularios para Ford F-100
        FormularioVehiculo::create([
            'vehiculo_id' => $vehiculo3->id,
            'tipo_formulario' => 'Formulario 08',
            'presentado' => false,
            'archivo_path' => null,
            'fecha_presentacion' => Carbon::now()->addDays(12),
            'observaciones' => 'Pendiente de presentación, propietario retrasado',
        ]);

        FormularioVehiculo::create([
            'vehiculo_id' => $vehiculo3->id,
            'tipo_formulario' => 'Libre de Deuda',
            'presentado' => true,
            'archivo_path' => '/documentos/vehiculos/GHI789_libredeuda.pdf',
            'fecha_presentacion' => Carbon::now()->subDays(8),
            'observaciones' => null,
        ]);

        // Formulario para Honda CG 150
        FormularioVehiculo::create([
            'vehiculo_id' => $vehiculo4->id,
            'tipo_formulario' => 'Formulario 12',
            'presentado' => true,
            'archivo_path' => '/documentos/vehiculos/JKL012_form12.pdf',
            'fecha_presentacion' => Carbon::now()->subDays(2),
            'observaciones' => 'Documentación reciente',
        ]);

        // Formularios para Chevrolet Tracker
        FormularioVehiculo::create([
            'vehiculo_id' => $vehiculo5->id,
            'tipo_formulario' => 'Libre de Deuda',
            'presentado' => true,
            'archivo_path' => '/documentos/vehiculos/MNO345_libredeuda.pdf',
            'fecha_presentacion' => Carbon::now()->subDays(5),
            'observaciones' => 'Documentación al día',
        ]);

        // Formulario para Renault Kangoo
        FormularioVehiculo::create([
            'vehiculo_id' => $vehiculo6->id,
            'tipo_formulario' => 'Formulario 02',
            'presentado' => false,
            'archivo_path' => null,
            'fecha_presentacion' => Carbon::now()->addDays(3),
            'observaciones' => 'En proceso de obtención',
        ]);
    }
}
