<?php

namespace App\Livewire\Configuracion;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;

use App\Models\Empresa;

class SeguridadIndex extends Component
{
    public $password_length;
    public $password_special_char;
    public $password_mixed_case;
    public $session_timeout;
    public $session_close_others;
    public $login_lockout;

    public function mount()
    {
        $this->authorize('empresa.gestionar');
        
        $empresa = Empresa::actual();
        $this->password_length = $empresa->password_length ?? 10;
        $this->password_special_char = (bool)($empresa->password_special_char ?? true);
        $this->password_mixed_case = (bool)($empresa->password_mixed_case ?? true);
        $this->session_timeout = $empresa->session_timeout ?? 30;
        $this->session_close_others = (bool)($empresa->session_close_others ?? true);
        $this->login_lockout = (bool)($empresa->login_lockout ?? true);
    }

    public function guardarSeguridad()
    {
        $this->authorize('empresa.gestionar');

        $empresa = Empresa::actual();
        $empresa->update([
            'password_length' => $this->password_length,
            'password_special_char' => $this->password_special_char,
            'password_mixed_case' => $this->password_mixed_case,
            'session_timeout' => $this->session_timeout,
            'session_close_others' => $this->session_close_others,
            'login_lockout' => $this->login_lockout,
        ]);

        $this->dispatch('toast', message: 'Configuración de seguridad guardada.', type: 'success');
    }

    #[Computed]
    public function indicePostura()
    {
        $score = 0;
        if ($this->password_length >= 12) $score += 25;
        elseif ($this->password_length >= 8) $score += 15;
        
        if ($this->password_special_char) $score += 15;
        if ($this->password_mixed_case) $score += 15;
        if ($this->session_timeout <= 30) $score += 15;
        if ($this->session_close_others) $score += 15;
        if ($this->login_lockout) $score += 15;
        
        return min(100, $score);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.configuracion.seguridad-index', [
            'empresa' => Empresa::actual(),
        ]);
    }
}
