<?php

namespace Tests\Feature;

use App\Livewire\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    /**
     * La página de perfil se muestra correctamente.
     */
    public function test_la_pagina_de_perfil_se_muestra(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/profile');

        $response->assertOk();
    }

    /**
     * La información del perfil puede actualizarse.
     */
    public function test_la_informacion_del_perfil_puede_actualizarse(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('nombre', 'Test User')
            ->set('email', 'test@example.com')
            ->call('actualizarPerfil')
            ->assertHasNoErrors()
            ->assertDispatched('toast');

        $user->refresh();

        $this->assertSame('Test User', $user->name);
        $this->assertSame('test@example.com', $user->email);
        $this->assertNull($user->email_verified_at);
    }

    /**
     * El estado de verificación del email no cambia si el email es el mismo.
     */
    public function test_el_estado_de_verificacion_no_cambia_si_el_email_no_cambia(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('nombre', 'Test User')
            ->set('email', $user->email)
            ->call('actualizarPerfil')
            ->assertHasNoErrors();

        $this->assertNotNull($user->refresh()->email_verified_at);
    }

    /**
     * Al elegir una foto se guarda automáticamente.
     */
    public function test_guarda_la_foto_de_perfil(): void
    {
        Storage::fake('public');

        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('avatar', UploadedFile::fake()->image('avatar.png'))
            ->assertHasNoErrors()
            ->assertSet('avatar', null)
            ->assertDispatched('toast');

        $user->refresh();

        $this->assertNotNull($user->avatar_path);
        Storage::disk('public')->assertExists($user->avatar_path);
    }

    /**
     * La foto de perfil puede eliminarse.
     */
    public function test_elimina_la_foto_de_perfil(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('avatars/avatar-anterior.png', 'contenido');

        $user = User::factory()->create(['avatar_path' => 'avatars/avatar-anterior.png']);

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->call('eliminarAvatar')
            ->assertDispatched('toast');

        $this->assertNull($user->refresh()->avatar_path);
        Storage::disk('public')->assertMissing('avatars/avatar-anterior.png');
    }

    /**
     * Un usuario puede eliminar su cuenta.
     */
    public function test_un_usuario_puede_eliminar_su_cuenta(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('password_eliminar', 'password')
            ->call('eliminarCuenta')
            ->assertHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();
        $this->assertNull($user->fresh());
    }

    /**
     * Se debe proporcionar la contraseña correcta para eliminar la cuenta.
     */
    public function test_se_requiere_la_contrasena_correcta_para_eliminar_la_cuenta(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('password_eliminar', 'wrong-password')
            ->call('eliminarCuenta')
            ->assertHasErrors('password_eliminar');

        $this->assertNotNull($user->fresh());
    }
}
