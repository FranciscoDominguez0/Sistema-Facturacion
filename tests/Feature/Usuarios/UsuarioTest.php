<?php

namespace Tests\Feature\Usuarios;

use App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * Tests de CRUD básico de usuarios (crear, editar, eliminar).
 *
 * No existe aún un módulo de gestión de usuarios en la aplicación,
 * por lo que estos tests cubren el modelo User directamente.
 */
class UsuarioTest extends TestCase
{
    use RefreshDatabase;

    // =====================================================================
    // Crear usuario
    // =====================================================================

    /**
     * Se puede crear un usuario y la contraseña queda hasheada (nunca en texto plano).
     */
    public function test_se_puede_crear_un_usuario_con_la_contrasena_hasheada(): void
    {
        $usuario = User::create([
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
            'password' => 'contrasena-segura',
        ]);

        $this->assertDatabaseHas('users', [
            'id' => $usuario->id,
            'name' => 'Juan Pérez',
            'email' => 'juan@example.com',
        ]);

        $this->assertNotSame('contrasena-segura', $usuario->password);
        $this->assertTrue(Hash::check('contrasena-segura', $usuario->password));
    }

    /**
     * No se pueden crear dos usuarios con el mismo email (restricción unique).
     */
    public function test_no_se_pueden_crear_dos_usuarios_con_el_mismo_email(): void
    {
        User::factory()->create(['email' => 'repetido@example.com']);

        $this->expectExceptionMessageMatches('/Duplicate|duplicada|ya existe|constraint/i');

        User::create([
            'name' => 'Otro Usuario',
            'email' => 'repetido@example.com',
            'password' => 'contrasena-segura',
        ]);
    }

    // =====================================================================
    // Editar usuario
    // =====================================================================

    /**
     * Se pueden editar los datos de un usuario (nombre y email).
     */
    public function test_se_puede_editar_un_usuario(): void
    {
        $usuario = User::factory()->create([
            'name' => 'Nombre Original',
            'email' => 'original@example.com',
        ]);

        $usuario->update([
            'name' => 'Nombre Actualizado',
            'email' => 'actualizado@example.com',
        ]);

        $usuario->refresh();

        $this->assertSame('Nombre Actualizado', $usuario->name);
        $this->assertSame('actualizado@example.com', $usuario->email);
    }

    // =====================================================================
    // Eliminar usuario
    // =====================================================================

    /**
     * Se puede eliminar un usuario y desaparece de la base de datos.
     */
    public function test_se_puede_eliminar_un_usuario(): void
    {
        $usuario = User::factory()->create();

        $usuario->delete();

        $this->assertDatabaseMissing('users', ['id' => $usuario->id]);
        $this->assertNull(User::find($usuario->id));
    }


    // =====================================================================
    // Seguridad
    // =====================================================================

    /**
     * La contraseña nunca se expone al serializar el usuario (oculta en el modelo).
     */
    public function test_la_contrasena_no_se_expone_al_serializar_el_usuario(): void
    {
        $usuario = User::factory()->create(['password' => 'contrasena-super-secreta']);

        $json = $usuario->toJson();

        $this->assertStringNotContainsString('contrasena-super-secreta', $json);
        $this->assertStringNotContainsString('"password"', $json);
    }
}
