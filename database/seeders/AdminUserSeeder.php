<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Asegurar que los roles necesarios existen
        $roleAdmin = Role::firstOrCreate(['name' => 'Administrador']);
        Role::firstOrCreate(['name' => 'Vendedor']);

        $permisos = [
            'empresa.gestionar',
            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.eliminar',
            'clientes.ver',
            'clientes.crear',
            'clientes.editar',
            'clientes.eliminar',
            'productos.ver',
            'productos.crear',
            'productos.editar',
            'productos.eliminar',
            'facturas.ver',
            'facturas.crear',
            'facturas.editar',
            'facturas.eliminar',
            'facturas.estado.cambiar',
            'facturas.vendedor.seleccionar',
            'facturas.descuento',
            'gastos.ver',
            'gastos.crear',
            'gastos.editar',
            'gastos.eliminar',
        ];

        foreach ($permisos as $permiso) {
            $p = Permission::firstOrCreate(['name' => $permiso]);
            $roleAdmin->givePermissionTo($p);
        }

        // 2. Crear al usuario
        $user = User::updateOrCreate(
            ['email' => 'dominguezf225@gmail.com'],
            [
                'name' => 'francisco Dominguez',
                'password' => Hash::make('4e369CBEAD'),
            ]
        );

        // 3. Asignar el rol al usuario
        $user->assignRole($roleAdmin);
    }
}
