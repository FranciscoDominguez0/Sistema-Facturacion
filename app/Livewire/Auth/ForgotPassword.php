<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Password;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.guest')]
class ForgotPassword extends Component
{
    public string $email = '';

    public string $status = '';

    public function sendPasswordResetLink(): void
    {
        $this->validate([
            'email' => ['required', 'string', 'email'],
        ]);

        // Ignoramos el estado de retorno para evitar enumeración de usuarios
        Password::broker()->sendResetLink(['email' => $this->email]);

        // Mensaje genérico por seguridad (User Enumeration prevention)
        $this->status = 'Si el correo existe en nuestro sistema, te hemos enviado un enlace para restablecer la contraseña.';
        $this->email = '';
    }

    public function render()
    {
        return view('livewire.auth.forgot-password');
    }
}
