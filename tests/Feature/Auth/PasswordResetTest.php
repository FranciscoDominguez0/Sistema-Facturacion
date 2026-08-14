<?php

namespace Tests\Feature\Auth;

use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Auth\ResetPassword;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Tests de recuperación de contraseña (solicitud de enlace y restablecimiento).
 */
class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    /**
     * La pantalla de solicitud de enlace de recuperación se muestra correctamente.
     */
    public function test_la_pantalla_de_solicitud_de_recuperacion_se_muestra(): void
    {
        $this->get('/forgot-password')
            ->assertOk()
            ->assertSeeLivewire(ForgotPassword::class);
    }

    /**
     * Se envía el enlace de recuperación si el email existe en el sistema.
     */
    public function test_se_envia_el_enlace_de_recuperacion_si_el_email_existe(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        Livewire::test(ForgotPassword::class)
            ->set('email', $user->email)
            ->call('sendPasswordResetLink')
            ->assertHasNoErrors()
            ->assertSet('email', '');

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    /**
     * No se revela si un email existe: con un email desconocido se muestra
     * el mismo mensaje genérico y no se envía ninguna notificación.
     */
    public function test_no_se_revela_si_un_email_no_existe(): void
    {
        Notification::fake();

        Livewire::test(ForgotPassword::class)
            ->set('email', 'no-existe@example.com')
            ->call('sendPasswordResetLink')
            ->assertHasNoErrors()
            ->assertSet('email', '')
            ->assertSet('status', 'Si el correo existe en nuestro sistema, te hemos enviado un enlace para restablecer la contraseña.');

        Notification::assertNothingSent();
    }

    /**
     * La pantalla de restablecimiento de contraseña se muestra con el token.
     */
    public function test_la_pantalla_de_restablecimiento_se_muestra(): void
    {
        $this->get('/reset-password/token-de-prueba')
            ->assertOk()
            ->assertSeeLivewire(ResetPassword::class);
    }

    /**
     * La contraseña puede restablecerse con un token válido.
     */
    public function test_la_contrasena_puede_restablecerse_con_un_token_valido(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        Livewire::test(ForgotPassword::class)
            ->set('email', $user->email)
            ->call('sendPasswordResetLink');

        Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) use ($user) {
            Livewire::test(ResetPassword::class, ['token' => $notification->token])
                ->set('email', $user->email)
                ->set('password', 'nueva-contrasena')
                ->set('password_confirmation', 'nueva-contrasena')
                ->call('resetPassword')
                ->assertHasNoErrors()
                ->assertRedirect(route('login'));

            $this->assertTrue(Hash::check('nueva-contrasena', $user->fresh()->password));

            return true;
        });
    }

    /**
     * La contraseña no se restablece con un token inválido.
     */
    public function test_la_contrasena_no_se_restablece_con_un_token_invalido(): void
    {
        $user = User::factory()->create();

        Livewire::test(ResetPassword::class, ['token' => 'token-invalido'])
            ->set('email', $user->email)
            ->set('password', 'nueva-contrasena')
            ->set('password_confirmation', 'nueva-contrasena')
            ->call('resetPassword')
            ->assertHasErrors('email')
            ->assertNoRedirect();

        $this->assertFalse(Hash::check('nueva-contrasena', $user->fresh()->password));
    }
}
