<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Batería de tests para la funcionalidad de Login.
 *
 * El proyecto usa PHPUnit clásico (no Pest), por lo que cada caso
 * se define como método test_* con un comentario descriptivo en español.
 */
class LoginTest extends TestCase
{
    use RefreshDatabase;

    // =====================================================================
    // Renderizado de la vista
    // =====================================================================

    /**
     * La vista de login carga correctamente y contiene el formulario
     * con los campos de email, contraseña y el enlace de recuperación.
     */
    public function test_la_vista_de_login_se_renderiza_con_el_formulario(): void
    {
        $response = $this->get('/login');

        $response
            ->assertOk()
            ->assertSeeLivewire(Login::class)
            ->assertSee('Iniciar sesión')
            ->assertSee('Contraseña')
            ->assertSee(route('password.request'));
    }

    /**
     * Un usuario ya autenticado que visite /login es redirigido al dashboard.
     */
    public function test_un_usuario_autenticado_es_redirigido_al_dashboard_al_visitar_login(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->get('/login')
            ->assertRedirect(route('dashboard', absolute: false));
    }

    // =====================================================================
    // Validación de campos
    // =====================================================================

    /**
     * El login falla si el campo email está vacío.
     */
    public function test_el_login_falla_si_el_email_esta_vacio(): void
    {
        Livewire::test(Login::class)
            ->set('email', '')
            ->set('password', 'password')
            ->call('login')
            ->assertHasErrors(['email' => 'required'])
            ->assertNoRedirect();

        $this->assertGuest();
    }

    /**
     * El login falla si el campo email no tiene un formato válido.
     */
    public function test_el_login_falla_si_el_email_no_es_valido(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'email-no-valido')
            ->set('password', 'password')
            ->call('login')
            ->assertHasErrors(['email' => 'email'])
            ->assertNoRedirect();

        $this->assertGuest();
    }

    /**
     * El login falla si el campo contraseña está vacío.
     */
    public function test_el_login_falla_si_la_contrasena_esta_vacia(): void
    {
        Livewire::test(Login::class)
            ->set('email', 'test@example.com')
            ->set('password', '')
            ->call('login')
            ->assertHasErrors(['password' => 'required'])
            ->assertNoRedirect();

        $this->assertGuest();
    }

    // =====================================================================
    // Autenticación exitosa
    // =====================================================================

    /**
     * Un usuario con credenciales correctas puede iniciar sesión,
     * es redirigido al dashboard y queda autenticado.
     */
    public function test_un_usuario_puede_iniciar_sesion_con_credenciales_correctas(): void
    {
        $user = User::factory()->create();

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
    }

    // =====================================================================
    // Autenticación fallida
    // =====================================================================

    /**
     * Con un email inexistente se muestra el mensaje de error genérico,
     * sin revelar si el email existe o no.
     */
    public function test_login_fallido_con_email_inexistente_muestra_error_generico(): void
    {
        $component = Livewire::test(Login::class)
            ->set('email', 'no-existe@example.com')
            ->set('password', 'password')
            ->call('login');

        $component
            ->assertHasErrors('email')
            ->assertNoRedirect()
            ->assertSee('Las credenciales proporcionadas no son correctas.');

        $this->assertGuest();
    }

    /**
     * Con una contraseña incorrecta se muestra el mismo mensaje de error
     * genérico (el sistema no debe diferenciar entre ambos casos).
     */
    public function test_login_fallido_con_contrasena_incorrecta_muestra_el_mismo_error_generico(): void
    {
        $user = User::factory()->create();

        $component = Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'contrasena-incorrecta')
            ->call('login');

        $component
            ->assertHasErrors('email')
            ->assertNoRedirect()
            ->assertSee('Las credenciales proporcionadas no son correctas.');

        $this->assertGuest();
    }

    // =====================================================================
    // Checkbox "Recordarme"
    // =====================================================================

    /**
     * Si se marca "Recordarme" se genera el remember_token en la base de
     * datos y se emite la cookie de recuerdo persistente.
     */
    public function test_con_recordarme_activado_se_genera_token_de_recuerdo_persistente(): void
    {
        $user = User::factory()->create(['remember_token' => null]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->set('remember', true)
            ->call('login');

        $this->assertAuthenticatedAs($user);
        $this->assertNotNull($user->refresh()->remember_token);
        $this->assertTrue($this->hasQueuedCookie(Auth::guard()->getRecallerName()));
    }

    /**
     * Si NO se marca "Recordarme" no se genera la cookie de recuerdo
     * persistente ni el remember_token en la base de datos.
     */
    public function test_sin_recordarme_no_se_genera_cookie_de_recuerdo_persistente(): void
    {
        $user = User::factory()->create(['remember_token' => null]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->set('remember', false)
            ->call('login');

        $this->assertAuthenticatedAs($user);
        $this->assertNull($user->refresh()->remember_token);
        $this->assertFalse($this->hasQueuedCookie(Auth::guard()->getRecallerName()));
    }

    // =====================================================================
    // Seguridad y rutas
    // =====================================================================

    /**
     * El registro público está desactivado: /register responde 404.
     */
    public function test_la_ruta_de_registro_publico_no_existe(): void
    {
        $this->get('/register')->assertNotFound();
    }

    /**
     * La recuperación de contraseña está activa: la ruta de solicitud
     * de enlace y la de restablecimiento son accesibles.
     */
    public function test_la_recuperacion_de_contrasena_esta_disponible(): void
    {
        $this->get('/forgot-password')
            ->assertOk()
            ->assertSeeLivewire(ForgotPassword::class);

        $this->get('/reset-password/token-de-prueba')->assertOk();
    }

    /**
     * Rate limiting: tras varios intentos fallidos el login debería
     * bloquearse temporalmente (RateLimiter).
     *
     * NOTA: este test está deshabilitado porque el rate limiting NO está
     * implementado en el componente activo (app/Livewire/Auth/Login.php).
     * Existe la clase app/Livewire/Forms/LoginForm.php con RateLimiter,
     * pero el componente actual no la utiliza. Habilitar este test cuando
     * se integre el límite de intentos.
     */
    public function test_el_login_se_bloquea_temporalmente_despues_de_varios_intentos_fallidos(): void
    {
        $this->markTestSkipped(
            'Rate limiting no implementado en el componente activo de Login.'
        );
    }

    // =====================================================================
    // Logout
    // =====================================================================

    /**
     * Un usuario autenticado puede cerrar sesión: se le redirige al login
     * y deja de estar autenticado.
     */
    public function test_un_usuario_autenticado_puede_cerrar_sesion(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->post('/logout')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    /**
     * Comprueba si una cookie fue añadida a la cola de cookies de la
     * respuesta (las respuestas JSON de Livewire no exponen assertHasCookie).
     */
    protected function hasQueuedCookie(string $name): bool
    {
        return collect($this->app['cookie']->getQueuedCookies())
            ->contains(fn ($cookie) => $cookie->getName() === $name);
    }
}
