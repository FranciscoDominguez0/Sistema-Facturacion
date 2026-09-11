<?php

namespace Tests\Feature\Auth;

use App\Livewire\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Un usuario autenticado puede actualizar su contraseña.
     */
    public function test_el_usuario_puede_actualizar_su_contrasena(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('password_actual', 'password')
            ->set('password_nueva', 'new-password')
            ->set('password_nueva_confirmation', 'new-password')
            ->call('actualizarPassword')
            ->assertHasNoErrors()
            ->assertDispatched('toast');

        $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
    }

    /**
     * Se debe proporcionar la contraseña actual correcta para actualizarla.
     */
    public function test_se_requiere_la_contrasena_actual_correcta_para_actualizarla(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(Profile::class)
            ->set('password_actual', 'wrong-password')
            ->set('password_nueva', 'new-password')
            ->set('password_nueva_confirmation', 'new-password')
            ->call('actualizarPassword')
            ->assertHasErrors(['password_actual']);
    }
}
