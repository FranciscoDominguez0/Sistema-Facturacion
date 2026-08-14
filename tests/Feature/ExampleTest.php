<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * La raíz de la aplicación redirige a la página de login.
     */
    public function test_la_raiz_de_la_aplicacion_redirige_al_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }
}
