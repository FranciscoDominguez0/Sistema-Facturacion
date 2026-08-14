<?php

namespace App\Livewire\Forms;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LoginForm extends Form
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    /**
     * Autentica al usuario con las credenciales del formulario.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->normalizeEmail();
        $this->validate();
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only(['email', 'password']), $this->remember)) {
            RateLimiter::hit($this->throttleKey(), $this->decaySeconds());

            // Limpia la contraseña para evitar dejarla en el formulario
            $this->reset('password');

            // Error genérico: no revela si el email existe (anti-enumeración)
            throw ValidationException::withMessages([
                'form.email' => trans('auth.failed'),
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Normaliza el email (sin espacios y en minúsculas) antes de validar.
     */
    protected function normalizeEmail(): void
    {
        $this->email = Str::lower(trim($this->email));
    }

    /**
     * Bloquea temporalmente el intento tras demasiados fallos.
     *
     * @throws ValidationException
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), $this->maxAttempts())) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.email' => trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function maxAttempts(): int
    {
        return (int) config('auth.max_attempts', 5);
    }

    protected function decaySeconds(): int
    {
        return (int) config('auth.decay_seconds', 60);
    }

    /**
     * Clave única del rate limiter: email + IP.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}
