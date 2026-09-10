<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermisosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permisos = [
            // Facturas
            'facturas.ver',
            'facturas.crear',
            'facturas.editar',
            'facturas.eliminar',

            // Clientes
            'clientes.ver',
            'clientes.crear',
            'clientes.editar',
            'clientes.eliminar',

            // Vendedores
            'vendedores.ver',
            'vendedores.crear',
            'vendedores.editar',
            'vendedores.eliminar',

            // Usuarios
            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.eliminar',

            // Productos
            'productos.ver',
            'productos.crear',
            'productos.editar',
            'productos.eliminar',

            // Gastos
            'gastos.ver',
            'gastos.crear',
            'gastos.editar',
            'gastos.eliminar',

            // Configuración general
            'empresa.gestionar',
            'reportes.ver',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso]);
        }
    }
}
