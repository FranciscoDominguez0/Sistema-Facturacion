<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\Login;
use App\Livewire\Dashboard;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Tests de autenticación: renderizado del login, acceso al dashboard
 * y cierre de sesión. La lógica completa del login está en LoginTest.
 */
class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * La vista de login se muestra correctamente con su componente Livewire.
     */
    public function test_la_vista_de_login_se_muestra_correctamente(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSeeLivewire(Login::class);
    }

    /**
     * Un usuario autenticado puede ver el dashboard con el menú de navegación.
     */
    public function test_un_usuario_autenticado_puede_ver_el_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->get('/dashboard')
            ->assertOk()
            ->assertSeeLivewire(Dashboard::class)
            ->assertSee('Cerrar sesión');
    }

    /**
     * Un usuario autenticado puede cerrar sesión y es redirigido al login.
     */
    public function test_un_usuario_autenticado_puede_cerrar_sesion(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->post('/logout')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
