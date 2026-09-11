<?php

namespace App\Livewire;

use App\Livewire\Actions\Logout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class Profile extends Component
{
    use WithFileUploads;

    // Información personal
    public string $nombre = '';

    public string $email = '';

    // El archivo de la foto vive en el componente: los uploads de Livewire
    // no se soportan dentro de form objects.
    public $avatar;

    public string $avatar_path_actual = '';

    // Cambio de contraseña
    public string $password_actual = '';

    public string $password_nueva = '';

    public string $password_nueva_confirmation = '';

    // Eliminación de cuenta
    public string $password_eliminar = '';

    public function mount(): void
    {
        $usuario = Auth::user();

        $this->nombre = $usuario->name;
        $this->email = $usuario->email;
        $this->avatar_path_actual = $usuario->avatar_path ?? '';
    }

    public function updatedAvatar(): void
    {
        if (! $this->avatar) {
            return;
        }

        try {
            $this->validate(['avatar' => ['required', 'image', 'max:2048']]);
        } catch (ValidationException $e) {
            $this->avatar = null;
            $this->dispatch('toast', message: 'La foto debe ser una imagen de máximo 2 MB.', type: 'error');

            return;
        }

        $usuario = Auth::user();
        $this->eliminarArchivoAvatar();

        $ruta = $this->avatar->store('avatars', 'public');
        $usuario->update(['avatar_path' => $ruta]);
        $this->avatar_path_actual = $ruta;
        $this->avatar = null;

        $this->dispatch('toast', message: 'Foto de perfil actualizada.', type: 'success');
    }

    public function actualizarPerfil(): void
    {
        $this->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore(Auth::id())],
        ]);

        $usuario = Auth::user();

        $usuario->name = $this->nombre;
        $usuario->email = $this->email;

        if ($usuario->isDirty('email')) {
            $usuario->email_verified_at = null;
        }

        $usuario->save();

        $this->dispatch('toast', message: 'Perfil actualizado correctamente.', type: 'success');
    }

    public function eliminarAvatar(): void
    {
        $this->eliminarArchivoAvatar();

        Auth::user()->update(['avatar_path' => null]);
        $this->avatar_path_actual = '';

        $this->dispatch('toast', message: 'Foto de perfil eliminada.', type: 'success');
    }

    public function actualizarPassword(): void
    {
        try {
            $validado = $this->validate([
                'password_actual' => ['required', 'string', 'current_password'],
                'password_nueva' => ['required', 'string', Password::defaults(), 'confirmed'],
            ]);
        } catch (ValidationException $e) {
            $this->reset('password_actual', 'password_nueva', 'password_nueva_confirmation');

            throw $e;
        }

        Auth::user()->update([
            'password' => Hash::make($validado['password_nueva']),
        ]);

        $this->reset('password_actual', 'password_nueva', 'password_nueva_confirmation');

        $this->dispatch('toast', message: 'Contraseña actualizada correctamente.', type: 'success');
    }

    public function eliminarCuenta(Logout $logout): void
    {
        $this->validate([
            'password_eliminar' => ['required', 'string', 'current_password'],
        ]);

        tap(Auth::user(), $logout(...))->delete();

        $this->redirect('/');
    }

    /**
     * Borra del disco la foto de perfil actual si existe.
     */
    private function eliminarArchivoAvatar(): void
    {
        if ($this->avatar_path_actual && Storage::disk('public')->exists($this->avatar_path_actual)) {
            Storage::disk('public')->delete($this->avatar_path_actual);
        }
    }

    public function render()
    {
        return view('livewire.profile');
    }
}
