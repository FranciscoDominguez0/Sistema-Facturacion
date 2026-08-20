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
        // 1. Asegurar que el rol Administrador existe
        $roleAdmin = Role::firstOrCreate(['name' => 'Administrador']);

        // Crear permisos
        $permisoEmpresa = Permission::firstOrCreate(['name' => 'empresa.gestionar']);
        $roleAdmin->givePermissionTo($permisoEmpresa);

        $permisoGastosVer = Permission::firstOrCreate(['name' => 'gastos.ver']);
        $permisoGastosGestionar = Permission::firstOrCreate(['name' => 'gastos.gestionar']);
        $roleAdmin->givePermissionTo([$permisoGastosVer, $permisoGastosGestionar]);

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
