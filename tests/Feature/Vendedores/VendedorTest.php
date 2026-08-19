<?php

namespace Tests\Feature\Vendedores;

use App\Livewire\Auth\Login;
use App\Livewire\Vendedores\VendedorForm;
use App\Livewire\Vendedores\VendedorIndex;
use App\Models\User;
use App\Models\Vendedor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests de la funcionalidad de Vendedores.
 *
 * El proyecto usa PHPUnit clásico (no Pest), por lo que cada caso se
 * define como método test_* con nombres descriptivos en español.
 *
 * Regla de negocio cubierta: crear un vendedor SIEMPRE crea un User,
 * le asigna un rol y crea el Vendedor relacionado, todo dentro de una
 * transacción (todo o nada).
 */
class VendedorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Permisos y roles que deben existir en el sistema (se crean para el test)
        Permission::findOrCreate('vendedores.gestionar');
        Role::findOrCreate('Vendedor');
        Role::findOrCreate('Administrador');
    }

    // =====================================================================
    // Acceso y permisos
    // =====================================================================

    /**
     * Un usuario sin sesión no puede ver el listado: se redirige al login.
     */
    public function test_un_invitado_no_puede_acceder_al_listado(): void
    {
        $this->get(route('vendedores'))->assertRedirect(route('login'));
    }

    /**
     * Un usuario sin sesión no puede acceder al formulario de creación.
     */
    public function test_un_invitado_no_puede_acceder_al_formulario_de_creacion(): void
    {
        $this->get(route('vendedores.create'))->assertRedirect(route('login'));
    }

    /**
     * Un usuario sin sesión no puede acceder al formulario de edición.
     */
    public function test_un_invitado_no_puede_acceder_al_formulario_de_edicion(): void
    {
        $vendedor = Vendedor::factory()->create();

        $this->get(route('vendedores.edit', $vendedor))->assertRedirect(route('login'));
    }

    /**
     * Un usuario autenticado sin el permiso vendedores.gestionar recibe 403
     * en las tres rutas del módulo.
     */
    public function test_un_usuario_sin_permiso_recibe_403_en_las_rutas_de_vendedores(): void
    {
        $usuario = User::factory()->create();
        $vendedor = Vendedor::factory()->create();

        $this->actingAs($usuario)->get(route('vendedores'))->assertForbidden();
        $this->actingAs($usuario)->get(route('vendedores.create'))->assertForbidden();
        $this->actingAs($usuario)->get(route('vendedores.edit', $vendedor))->assertForbidden();
    }

    /**
     * Un usuario autenticado con el permiso vendedores.gestionar puede
     * acceder correctamente a las tres rutas del módulo.
     */
    public function test_un_usuario_con_permiso_puede_acceder_a_las_rutas_de_vendedores(): void
    {
        $usuario = $this->usuarioConGestion();
        $vendedor = Vendedor::factory()->create();

        $this->actingAs($usuario)->get(route('vendedores'))->assertOk();
        $this->actingAs($usuario)->get(route('vendedores.create'))->assertOk();
        $this->actingAs($usuario)->get(route('vendedores.edit', $vendedor))->assertOk();
    }

    /**
     * No existe registro público: la ruta /register no está registrada y
     * responde 404.
     */
    public function test_la_ruta_de_registro_publico_no_existe(): void
    {
        $this->assertFalse(Route::has('register'));

        $this->get('/register')->assertNotFound();
    }

    // =====================================================================
    // Listado (VendedorIndex)
    // =====================================================================

    /**
     * El listado muestra los datos reales del vendedor: nombre y email
     * desde users; código, comisión y estado desde vendedores.
     */
    public function test_el_listado_muestra_los_datos_de_los_vendedores(): void
    {
        $usuario = User::factory()->create([
            'name' => 'Lucía Fernández',
            'email' => 'lucia@ejemplo.com',
        ]);

        Vendedor::factory()->create([
            'user_id' => $usuario->id,
            'codigo' => 'VEN-001',
            'comision_porcentaje' => 5.00,
        ]);

        $componente = Livewire::test(VendedorIndex::class)
            ->assertSee('Lucía Fernández')
            ->assertSee('lucia@ejemplo.com')
            ->assertSee('VEN-001')
            ->assertSee('5.00%');

        // El estado se muestra con la etiqueta "Activo" dentro de su badge
        $this->assertMatchesRegularExpression('/>\s*Activo\s*<\/span>/', $componente->html());
    }

    /**
     * La búsqueda filtra por nombre.
     */
    public function test_la_busqueda_filtra_por_nombre(): void
    {
        $encontrado = Vendedor::factory()->create([
            'user_id' => User::factory()->create(['name' => 'Pedro Sánchez'])->id,
        ]);
        $otro = Vendedor::factory()->create([
            'user_id' => User::factory()->create(['name' => 'Ana Torres'])->id,
        ]);

        Livewire::test(VendedorIndex::class)
            ->set('search', 'Pedro')
            ->assertSee($encontrado->user->name)
            ->assertDontSee($otro->user->name);
    }

    /**
     * La búsqueda filtra por email.
     */
    public function test_la_busqueda_filtra_por_email(): void
    {
        $encontrado = Vendedor::factory()->create([
            'user_id' => User::factory()->create(['email' => 'pedro@ejemplo.com'])->id,
        ]);
        $otro = Vendedor::factory()->create([
            'user_id' => User::factory()->create(['email' => 'ana@otro.com'])->id,
        ]);

        Livewire::test(VendedorIndex::class)
            ->set('search', 'pedro@ejemplo.com')
            ->assertSee($encontrado->user->name)
            ->assertDontSee($otro->user->name);
    }

    /**
     * La búsqueda filtra por código.
     */
    public function test_la_busqueda_filtra_por_codigo(): void
    {
        $encontrado = Vendedor::factory()->create([
            'codigo' => 'VEN-100',
            'user_id' => User::factory()->create(['name' => 'Pedro Sánchez'])->id,
        ]);
        $otro = Vendedor::factory()->create([
            'codigo' => 'VEN-200',
            'user_id' => User::factory()->create(['name' => 'Ana Torres'])->id,
        ]);

        Livewire::test(VendedorIndex::class)
            ->set('search', 'VEN-100')
            ->assertSee($encontrado->user->name)
            ->assertDontSee($otro->user->name);
    }

    /**
     * Un vendedor activo se muestra con el estado "Activo" y uno inactivo
     * con "Inactivo"; el filtro de estado aísla a cada uno.
     */
    public function test_el_estado_activo_e_inactivo_se_muestra_correctamente(): void
    {
        $activo = Vendedor::factory()->create(['activo' => true]);
        $inactivo = Vendedor::factory()->inactivo()->create();

        $componente = Livewire::test(VendedorIndex::class);

        // Las etiquetas del estado se muestran dentro de sus badges
        $this->assertMatchesRegularExpression('/>\s*Activo\s*<\/span>/', $componente->html());
        $this->assertMatchesRegularExpression('/>\s*Inactivo\s*<\/span>/', $componente->html());

        Livewire::test(VendedorIndex::class)
            ->set('filtroEstado', 'Activo')
            ->assertSee($activo->user->name)
            ->assertDontSee($inactivo->user->name);

        Livewire::test(VendedorIndex::class)
            ->set('filtroEstado', 'Inactivo')
            ->assertSee($inactivo->user->name)
            ->assertDontSee($activo->user->name);
    }

    /**
     * El botón activar/desactivar cambia vendedores.activo sin borrar el
     * registro: sigue existiendo, solo cambia su estado.
     */
    public function test_toggle_activo_cambia_el_estado_sin_eliminar_el_registro(): void
    {
        $usuario = $this->usuarioConGestion();
        $vendedor = Vendedor::factory()->create();

        Livewire::actingAs($usuario)
            ->test(VendedorIndex::class)
            ->call('toggleActivo', $vendedor->id);

        $this->assertDatabaseCount('vendedores', 1);
        $this->assertDatabaseHas('vendedores', [
            'id' => $vendedor->id,
            'activo' => false,
        ]);

        // Segundo toggle: el vendedor vuelve a activarse
        Livewire::actingAs($usuario)
            ->test(VendedorIndex::class)
            ->call('toggleActivo', $vendedor->id);

        $this->assertDatabaseHas('vendedores', [
            'id' => $vendedor->id,
            'activo' => true,
        ]);
    }

    /**
     * Un usuario sin el permiso no puede cambiar el estado de un vendedor.
     */
    public function test_un_usuario_sin_permiso_no_puede_cambiar_el_estado(): void
    {
        $usuario = User::factory()->create();
        $vendedor = Vendedor::factory()->create();

        Livewire::actingAs($usuario)
            ->test(VendedorIndex::class)
            ->call('toggleActivo', $vendedor->id)
            ->assertForbidden();

        $this->assertDatabaseHas('vendedores', [
            'id' => $vendedor->id,
            'activo' => true,
        ]);
    }

    // =====================================================================
    // Creación (VendedorForm — modo crear)
    // =====================================================================

    /**
     * Crear un vendedor con datos válidos crea un User y un Vendedor
     * relacionados correctamente.
     */
    public function test_crear_vendedor_crea_un_usuario_y_un_vendedor_relacionados(): void
    {
        $usuario = $this->usuarioConGestion();

        Livewire::actingAs($usuario)
            ->test(VendedorForm::class)
            ->set('form.nombre', 'Laura Méndez')
            ->set('form.email', 'laura@ejemplo.com')
            ->set('form.password', 'contrasena-123')
            ->set('form.rol', 'Vendedor')
            ->call('save')
            ->assertHasNoErrors();

        // Solo se creó el vendedor con su usuario (el usuario de gestión ya existía)
        $this->assertSame(1, User::where('email', 'laura@ejemplo.com')->count());
        $this->assertDatabaseCount('vendedores', 1);

        $user = User::where('email', 'laura@ejemplo.com')->firstOrFail();
        $vendedor = Vendedor::where('user_id', $user->id)->firstOrFail();

        $this->assertSame($user->id, $vendedor->user_id);
    }

    /**
     * El rol seleccionado queda asignado al User creado.
     */
    public function test_el_rol_se_asigna_al_usuario_creado(): void
    {
        $usuario = $this->usuarioConGestion();

        Livewire::actingAs($usuario)
            ->test(VendedorForm::class)
            ->set('form.nombre', 'Laura Méndez')
            ->set('form.email', 'laura@ejemplo.com')
            ->set('form.password', 'contrasena-123')
            ->set('form.rol', 'Vendedor')
            ->call('save')
            ->assertHasNoErrors();

        $user = User::where('email', 'laura@ejemplo.com')->firstOrFail();

        $this->assertTrue($user->hasRole('Vendedor'));
    }

    /**
     * La contraseña se guarda hasheada, nunca en texto plano.
     */
    public function test_la_contrasena_se_guarda_hasheada(): void
    {
        $usuario = $this->usuarioConGestion();
        $passwordPlano = 'contrasena-123';

        Livewire::actingAs($usuario)
            ->test(VendedorForm::class)
            ->set('form.nombre', 'Laura Méndez')
            ->set('form.email', 'laura@ejemplo.com')
            ->set('form.password', $passwordPlano)
            ->set('form.rol', 'Vendedor')
            ->call('save')
            ->assertHasNoErrors();

        $user = User::where('email', 'laura@ejemplo.com')->firstOrFail();

        $this->assertNotEquals($passwordPlano, $user->password);
        $this->assertTrue(Hash::check($passwordPlano, $user->password));
    }

    /**
     * El vendedor recién creado puede iniciar sesión con la contraseña
     * ingresada al crearlo.
     */
    public function test_el_vendedor_recien_creado_puede_iniciar_sesion(): void
    {
        $usuario = $this->usuarioConGestion();

        Livewire::actingAs($usuario)
            ->test(VendedorForm::class)
            ->set('form.nombre', 'Carlos Ríos')
            ->set('form.email', 'carlos@ejemplo.com')
            ->set('form.password', 'contrasena-123')
            ->set('form.rol', 'Vendedor')
            ->call('save')
            ->assertHasNoErrors();

        $user = User::where('email', 'carlos@ejemplo.com')->firstOrFail();

        // Se cierra la sesión del usuario de gestión para probar el login limpio
        Auth::logout();

        Livewire::test(Login::class)
            ->set('form.email', 'carlos@ejemplo.com')
            ->set('form.password', 'contrasena-123')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($user);
    }

    /**
     * La validación falla si falta el nombre y no se crea ningún registro.
     */
    public function test_falla_la_validacion_si_falta_el_nombre(): void
    {
        $usuario = $this->usuarioConGestion();

        Livewire::actingAs($usuario)
            ->test(VendedorForm::class)
            ->set('form.nombre', '')
            ->set('form.email', 'laura@ejemplo.com')
            ->set('form.password', 'contrasena-123')
            ->set('form.rol', 'Vendedor')
            ->call('save')
            ->assertHasErrors(['form.nombre' => 'required']);

        $this->assertFalse(User::where('email', 'laura@ejemplo.com')->exists());
        $this->assertDatabaseCount('vendedores', 0);
    }

    /**
     * La validación falla si el email ya existe en users y no se crea
     * ningún registro nuevo.
     */
    public function test_falla_la_validacion_si_el_email_ya_esta_en_uso(): void
    {
        $usuario = $this->usuarioConGestion();
        User::factory()->create(['email' => 'existente@ejemplo.com']);

        Livewire::actingAs($usuario)
            ->test(VendedorForm::class)
            ->set('form.nombre', 'Laura Méndez')
            ->set('form.email', 'existente@ejemplo.com')
            ->set('form.password', 'contrasena-123')
            ->set('form.rol', 'Vendedor')
            ->call('save')
            ->assertHasErrors(['form.email' => 'unique']);

        $this->assertSame(1, User::where('email', 'existente@ejemplo.com')->count());
        $this->assertDatabaseCount('vendedores', 0);
    }

    /**
     * La validación falla si falta la contraseña al crear.
     */
    public function test_falla_la_validacion_si_falta_la_contrasena(): void
    {
        $usuario = $this->usuarioConGestion();

        Livewire::actingAs($usuario)
            ->test(VendedorForm::class)
            ->set('form.nombre', 'Laura Méndez')
            ->set('form.email', 'laura@ejemplo.com')
            ->set('form.password', '')
            ->set('form.rol', 'Vendedor')
            ->call('save')
            ->assertHasErrors(['form.password' => 'required']);

        $this->assertFalse(User::where('email', 'laura@ejemplo.com')->exists());
        $this->assertDatabaseCount('vendedores', 0);
    }

    /**
     * La validación falla si la comisión o el descuento máximo están
     * fuera del rango 0-100.
     */
    public function test_falla_la_validacion_si_los_porcentajes_estan_fuera_de_rango(): void
    {
        $usuario = $this->usuarioConGestion();

        Livewire::actingAs($usuario)
            ->test(VendedorForm::class)
            ->set('form.nombre', 'Laura Méndez')
            ->set('form.email', 'laura@ejemplo.com')
            ->set('form.password', 'contrasena-123')
            ->set('form.rol', 'Vendedor')
            ->set('form.comision_porcentaje', '150')
            ->set('form.descuento_maximo_porcentaje', '-5')
            ->call('save')
            ->assertHasErrors([
                'form.comision_porcentaje' => 'max',
                'form.descuento_maximo_porcentaje' => 'min',
            ]);

        $this->assertFalse(User::where('email', 'laura@ejemplo.com')->exists());
        $this->assertDatabaseCount('vendedores', 0);
    }

    /**
     * Los campos opcionales (codigo, comision_porcentaje,
     * descuento_maximo_porcentaje) pueden quedar vacíos y el registro
     * se crea igual.
     */
    public function test_los_campos_opcionales_pueden_quedar_vacios(): void
    {
        $usuario = $this->usuarioConGestion();

        Livewire::actingAs($usuario)
            ->test(VendedorForm::class)
            ->set('form.nombre', 'Laura Méndez')
            ->set('form.email', 'laura@ejemplo.com')
            ->set('form.password', 'contrasena-123')
            ->set('form.rol', 'Vendedor')
            ->set('form.codigo', '')
            ->set('form.comision_porcentaje', '')
            ->set('form.descuento_maximo_porcentaje', '')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('vendedores', [
            'user_id' => User::where('email', 'laura@ejemplo.com')->firstOrFail()->id,
            'codigo' => null,
            'comision_porcentaje' => null,
        ]);
    }

    /**
     * activo queda en true por defecto al crear si no se toca el toggle.
     */
    public function test_el_vendedor_se_crea_activo_por_defecto(): void
    {
        $usuario = $this->usuarioConGestion();

        Livewire::actingAs($usuario)
            ->test(VendedorForm::class)
            ->set('form.nombre', 'Laura Méndez')
            ->set('form.email', 'laura@ejemplo.com')
            ->set('form.password', 'contrasena-123')
            ->set('form.rol', 'Vendedor')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('vendedores', [
            'user_id' => User::where('email', 'laura@ejemplo.com')->firstOrFail()->id,
            'activo' => true,
        ]);
    }

    // =====================================================================
    // Edición (VendedorForm — modo editar)
    // =====================================================================

    /**
     * Al editar, los campos del formulario cargan los datos reales del
     * vendedor y su usuario.
     */
    public function test_al_editar_los_campos_cargan_los_datos_del_vendedor(): void
    {
        $usuario = $this->usuarioConGestion();
        $vendedor = Vendedor::factory()->create([
            'codigo' => 'VEN-REAL',
            'comision_porcentaje' => 7.50,
            'descuento_maximo_porcentaje' => 12.00,
        ]);
        $vendedor->user->update(['name' => 'Nombre Real', 'email' => 'real@ejemplo.com']);
        $vendedor->user->assignRole('Vendedor');

        // Se recarga desde la base para comparar los valores decimales tal como se guardan
        $vendedor->refresh();

        Livewire::actingAs($usuario)
            ->test(VendedorForm::class, ['vendedor' => $vendedor])
            ->assertSet('form.nombre', 'Nombre Real')
            ->assertSet('form.email', 'real@ejemplo.com')
            ->assertSet('form.rol', 'Vendedor')
            ->assertSet('form.codigo', 'VEN-REAL')
            ->assertSet('form.comision_porcentaje', '7.50')
            ->assertSet('form.descuento_maximo_porcentaje', '12.00')
            ->assertSet('form.activo', true);
    }

    /**
     * Editar el nombre y el email actualiza el User correctamente.
     */
    public function test_editar_actualiza_el_nombre_y_email_del_usuario(): void
    {
        $usuario = $this->usuarioConGestion();
        $vendedor = Vendedor::factory()->create();
        $vendedor->user->update(['name' => 'Antes', 'email' => 'antes@ejemplo.com']);
        $vendedor->user->assignRole('Vendedor');

        Livewire::actingAs($usuario)
            ->test(VendedorForm::class, ['vendedor' => $vendedor])
            ->set('form.nombre', 'Después')
            ->set('form.email', 'despues@ejemplo.com')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $vendedor->user_id,
            'name' => 'Después',
            'email' => 'despues@ejemplo.com',
        ]);
    }

    /**
     * Editar sin llenar el campo password no cambia la contraseña original.
     */
    public function test_editar_sin_contrasena_no_cambia_el_password(): void
    {
        $usuario = $this->usuarioConGestion();
        $passwordOriginal = 'clave-original-123';
        $vendedor = Vendedor::factory()->create([
            'user_id' => User::factory()->create(['password' => Hash::make($passwordOriginal)])->id,
        ]);
        $vendedor->user->assignRole('Vendedor');

        Livewire::actingAs($usuario)
            ->test(VendedorForm::class, ['vendedor' => $vendedor])
            ->set('form.nombre', 'Nombre Nuevo')
            ->set('form.password', '')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertTrue(Hash::check($passwordOriginal, $vendedor->user->fresh()->password));
    }

    /**
     * Editar llenando el campo password sí actualiza la contraseña.
     */
    public function test_editar_con_nueva_contrasena_la_actualiza(): void
    {
        $usuario = $this->usuarioConGestion();
        $passwordOriginal = 'clave-original-123';
        $vendedor = Vendedor::factory()->create([
            'user_id' => User::factory()->create(['password' => Hash::make($passwordOriginal)])->id,
        ]);
        $vendedor->user->assignRole('Vendedor');

        Livewire::actingAs($usuario)
            ->test(VendedorForm::class, ['vendedor' => $vendedor])
            ->set('form.password', 'clave-nueva-456')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertFalse(Hash::check($passwordOriginal, $vendedor->user->fresh()->password));
        $this->assertTrue(Hash::check('clave-nueva-456', $vendedor->user->fresh()->password));
    }

    /**
     * Cambiar el rol al editar actualiza el rol asignado al User.
     */
    public function test_editar_cambia_el_rol_asignado(): void
    {
        $usuario = $this->usuarioConGestion();
        $vendedor = Vendedor::factory()->create();
        $vendedor->user->assignRole('Vendedor');

        Livewire::actingAs($usuario)
            ->test(VendedorForm::class, ['vendedor' => $vendedor])
            ->set('form.rol', 'Administrador')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertTrue($vendedor->user->fresh()->hasRole('Administrador'));
        $this->assertFalse($vendedor->user->fresh()->hasRole('Vendedor'));
    }

    /**
     * Al editar, la validación de email único ignora el propio registro:
     * guardar sin cambiar el email no debe fallar por "ya en uso".
     */
    public function test_editar_sin_cambiar_el_email_no_falla_por_estar_en_uso(): void
    {
        $usuario = $this->usuarioConGestion();
        $vendedor = Vendedor::factory()->create();
        $vendedor->user->assignRole('Vendedor');

        Livewire::actingAs($usuario)
            ->test(VendedorForm::class, ['vendedor' => $vendedor])
            ->set('form.nombre', 'Nombre Cambiado')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('users', [
            'id' => $vendedor->user_id,
            'name' => 'Nombre Cambiado',
            'email' => $vendedor->user->email,
        ]);
    }

    /**
     * Editar el codigo, la comisión y el descuento máximo actualiza
     * la tabla vendedores correctamente.
     */
    public function test_editar_actualiza_los_campos_del_vendedor(): void
    {
        $usuario = $this->usuarioConGestion();
        $vendedor = Vendedor::factory()->create([
            'codigo' => 'VEN-VIEJO',
            'comision_porcentaje' => 2.00,
            'descuento_maximo_porcentaje' => 5.00,
        ]);
        $vendedor->user->assignRole('Vendedor');

        Livewire::actingAs($usuario)
            ->test(VendedorForm::class, ['vendedor' => $vendedor])
            ->set('form.codigo', 'VEN-NUEVO')
            ->set('form.comision_porcentaje', '7.5')
            ->set('form.descuento_maximo_porcentaje', '15')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('vendedores', [
            'id' => $vendedor->id,
            'codigo' => 'VEN-NUEVO',
            'comision_porcentaje' => '7.50',
            'descuento_maximo_porcentaje' => '15.00',
        ]);
    }

    // =====================================================================
    // Atomicidad
    // =====================================================================

    /**
     * Si la creación del Vendedor falla después de crear el User, la
     * transacción revierte todo: el User no debe quedar creado.
     */
    public function test_si_falla_la_creacion_del_vendedor_se_revierte_el_usuario(): void
    {
        $usuario = $this->usuarioConGestion();

        // Simula un fallo justo antes de insertar el Vendedor, dentro de la transacción
        Vendedor::creating(function () {
            throw new \Exception('Error simulado al crear el vendedor');
        });

        try {
            Livewire::actingAs($usuario)
                ->test(VendedorForm::class)
                ->set('form.nombre', 'Vendedor Transacción')
                ->set('form.email', 'transaccion@ejemplo.com')
                ->set('form.password', 'contrasena-123')
                ->set('form.rol', 'Vendedor')
                ->call('save');

            $this->fail('La creación debió fallar y lanzar una excepción.');
        } catch (\Throwable $e) {
            $this->assertStringContainsString('Error simulado', $e->getMessage());
        }

        // Rollback completo: ni el User ni el Vendedor quedaron creados
        $this->assertFalse(User::where('email', 'transaccion@ejemplo.com')->exists());
        $this->assertDatabaseCount('vendedores', 0);
    }

    // =====================================================================
    // Reglas de integridad
    // =====================================================================

    /**
     * No existe una ruta ni una acción de borrado físico de vendedores:
     * los vendedores solo se desactivan. Si apareciera una ruta de este
     * tipo, este test debe fallar por diseño.
     */
    public function test_no_existe_ruta_de_eliminacion_fisica_de_vendedores(): void
    {
        $this->assertFalse(Route::has('vendedores.destroy'));

        $rutasDeBorrado = collect(Route::getRoutes())
            ->filter(fn ($ruta) => in_array('DELETE', $ruta->methods()) && str_starts_with($ruta->uri(), 'vendedores'));

        $this->assertTrue(
            $rutasDeBorrado->isEmpty(),
            'No debe existir una ruta DELETE bajo /vendedores: el borrado físico está prohibido por diseño.'
        );
    }

    /**
     * La relación users-vendedores se maneja a nivel de base de datos: la
     * FK de vendedores.user_id usa onDelete('cascade'), por lo que al
     * eliminar un User se elimina su Vendedor asociado (no queda huérfano).
     */
    public function test_al_eliminar_un_usuario_con_vendedor_asociado_la_relacion_se_maneja_por_fk(): void
    {
        $usuario = User::factory()->create();
        $vendedor = Vendedor::factory()->create(['user_id' => $usuario->id]);

        $usuario->delete();

        $this->assertDatabaseMissing('users', ['id' => $usuario->id]);
        $this->assertDatabaseMissing('vendedores', ['id' => $vendedor->id]);
    }

    // =====================================================================
    // Helpers
    // =====================================================================

    /**
     * Usuario con el permiso para gestionar vendedores.
     */
    protected function usuarioConGestion(): User
    {
        $usuario = User::factory()->create();
        $usuario->givePermissionTo('vendedores.gestionar');

        return $usuario;
    }
}
