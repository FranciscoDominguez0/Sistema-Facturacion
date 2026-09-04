<?php

namespace Tests\Feature\Productos;

use App\Livewire\Productos\ProductoForm;
use App\Livewire\Productos\ProductoIndex;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

/**
 * Tests de la funcionalidad de Productos y Servicios.
 *
 * El proyecto usa PHPUnit clásico (no Pest), por lo que cada caso se
 * define como método test_* con nombres descriptivos en español.
 */
class ProductoTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Permisos que deben existir en el sistema (se crean para el test)
        Permission::findOrCreate('productos.ver');
        Permission::findOrCreate('productos.crear');
        Permission::findOrCreate('productos.editar');
        Permission::findOrCreate('productos.eliminar');
    }

    // =====================================================================
    // Renderizado y acceso
    // =====================================================================

    /**
     * Un usuario con permiso productos.ver puede ver el listado.
     */
    public function test_un_usuario_con_permiso_puede_ver_el_listado(): void
    {
        $usuario = User::factory()->create();
        $usuario->givePermissionTo('productos.ver');

        $this->actingAs($usuario)->get('/productos')->assertOk();
    }

    /**
     * Un usuario sin permiso productos.ver no puede ver el listado.
     */
    public function test_un_usuario_sin_permiso_no_puede_ver_el_listado(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)->get('/productos')->assertForbidden();
    }

    /**
     * Un usuario no autenticado es redirigido al login.
     */
    public function test_un_usuario_no_autenticado_es_redirigido_al_login(): void
    {
        $this->get('/productos')->assertRedirect(route('login'));
    }

    // =====================================================================
    // Listado (ProductoIndex)
    // =====================================================================

    /**
     * El listado muestra los productos reales de la base de datos.
     */
    public function test_el_listado_muestra_los_productos_existentes(): void
    {
        $primero = Producto::factory()->create(['nombre' => 'Laptop Pro']);
        $segundo = Producto::factory()->create(['nombre' => 'Mouse Gamer']);

        Livewire::test(ProductoIndex::class)
            ->assertSee($primero->nombre)
            ->assertSee($segundo->nombre);
    }

    /**
     * La búsqueda en vivo filtra por nombre.
     */
    public function test_la_busqueda_filtra_por_nombre(): void
    {
        $encontrado = Producto::factory()->create(['nombre' => 'Laptop Pro']);
        $otro = Producto::factory()->create(['nombre' => 'Mouse Gamer']);

        Livewire::test(ProductoIndex::class)
            ->set('search', 'Laptop')
            ->assertSee($encontrado->nombre)
            ->assertDontSee($otro->nombre);
    }

    /**
     * La búsqueda en vivo filtra por código/SKU.
     */
    public function test_la_busqueda_filtra_por_codigo(): void
    {
        $encontrado = Producto::factory()->create(['codigo' => 'SKU-100']);
        $otro = Producto::factory()->create(['codigo' => 'SKU-200']);

        Livewire::test(ProductoIndex::class)
            ->set('search', 'SKU-100')
            ->assertSee($encontrado->nombre)
            ->assertDontSee($otro->nombre);
    }

    /**
     * El filtro por tipo (producto/servicio) funciona correctamente.
     */
    public function test_el_filtro_por_tipo_funciona(): void
    {
        $producto = Producto::factory()->create(['tipo' => 'producto', 'nombre' => 'Artículo Físico']);
        $servicio = Producto::factory()->create(['tipo' => 'servicio', 'nombre' => 'Servicio Técnico']);

        Livewire::test(ProductoIndex::class)
            ->set('filtroTipo', 'servicio')
            ->assertSee($servicio->nombre)
            ->assertDontSee($producto->nombre);
    }

    /**
     * El filtro por estado (activo/inactivo) funciona correctamente.
     */
    public function test_el_filtro_por_estado_funciona(): void
    {
        $activo = Producto::factory()->create(['nombre' => 'Producto Activo']);
        $inactivo = Producto::factory()->inactivo()->create(['nombre' => 'Producto Inactivo']);

        Livewire::test(ProductoIndex::class)
            ->set('filtroEstado', 'Activo')
            ->assertSee($activo->nombre)
            ->assertDontSee($inactivo->nombre);

        Livewire::test(ProductoIndex::class)
            ->set('filtroEstado', 'Inactivo')
            ->assertSee($inactivo->nombre)
            ->assertDontSee($activo->nombre);
    }

    /**
     * El listado pagina de 10 en 10 y no trae todos los registros.
     */
    public function test_el_listado_pagina_de_10_en_10(): void
    {
        $masViejo = Producto::factory()->create(['nombre' => 'Producto Más Viejo']);
        Producto::factory()->count(12)->create();
        $masNuevo = Producto::factory()->create(['nombre' => 'Producto Más Nuevo']);

        Livewire::test(ProductoIndex::class)
            ->assertSee($masNuevo->nombre)
            ->assertDontSee($masViejo->nombre);
    }

    /**
     * Sin productos en la base, se muestra el mensaje de estado vacío.
     */
    public function test_se_muestra_el_mensaje_cuando_no_hay_productos(): void
    {
        Livewire::test(ProductoIndex::class)
            ->assertSee('No hay productos registrados');
    }

    // =====================================================================
    // Validación del formulario (ProductoForm)
    // =====================================================================

    /**
     * El formulario falla si el nombre está vacío.
     */
    public function test_falla_si_el_nombre_esta_vacio(): void
    {
        $usuario = $this->usuarioConGestion();

        Livewire::actingAs($usuario)
            ->test(ProductoForm::class)
            ->set('form.nombre', '')
            ->set('form.precio', '50.00')
            ->call('save')
            ->assertHasErrors(['form.nombre' => 'required']);
    }

    /**
     * El formulario falla si el precio está vacío.
     */
    public function test_falla_si_el_precio_esta_vacio(): void
    {
        $usuario = $this->usuarioConGestion();

        Livewire::actingAs($usuario)
            ->test(ProductoForm::class)
            ->set('form.nombre', 'Producto Prueba')
            ->set('form.precio', '')
            ->call('save')
            ->assertHasErrors(['form.precio' => 'required']);
    }

    /**
     * El formulario falla si el precio es negativo.
     */
    public function test_falla_si_el_precio_es_negativo(): void
    {
        $usuario = $this->usuarioConGestion();

        Livewire::actingAs($usuario)
            ->test(ProductoForm::class)
            ->set('form.nombre', 'Producto Prueba')
            ->set('form.precio', '-5')
            ->call('save')
            ->assertHasErrors(['form.precio' => 'min']);
    }

    /**
     * El formulario falla si el precio no es numérico.
     */
    public function test_falla_si_el_precio_no_es_numerico(): void
    {
        $usuario = $this->usuarioConGestion();

        Livewire::actingAs($usuario)
            ->test(ProductoForm::class)
            ->set('form.nombre', 'Producto Prueba')
            ->set('form.precio', 'abc')
            ->call('save')
            ->assertHasErrors(['form.precio' => 'numeric']);
    }

    /**
     * Código, descripción e imagen son opcionales: no fallan si se omiten.
     */
    public function test_los_campos_opcionales_no_son_obligatorios(): void
    {
        $usuario = $this->usuarioConGestion();

        Livewire::actingAs($usuario)
            ->test(ProductoForm::class)
            ->set('form.nombre', 'Producto Sin Opcionales')
            ->set('form.precio', '10.00')
            ->call('save')
            ->assertHasNoErrors();
    }

    // =====================================================================
    // Creación exitosa
    // =====================================================================

    /**
     * Un usuario con permiso puede crear un producto con solo nombre y precio.
     */
    public function test_se_puede_crear_un_producto_con_solo_nombre_y_precio(): void
    {
        $usuario = $this->usuarioConGestion();

        Livewire::actingAs($usuario)
            ->test(ProductoForm::class)
            ->set('form.nombre', 'Producto Básico')
            ->set('form.precio', '25.50')
            ->call('save')
            ->assertHasNoErrors()
            ->assertRedirect(route('productos.index'));

        $this->assertDatabaseHas('productos', [
            'nombre' => 'Producto Básico',
            'precio' => '25.50',
        ]);
    }

    /**
     * El producto se guarda con activo = true por defecto.
     */
    public function test_el_producto_se_guarda_activo_por_defecto(): void
    {
        $usuario = $this->usuarioConGestion();

        Livewire::actingAs($usuario)
            ->test(ProductoForm::class)
            ->set('form.nombre', 'Producto Activo Default')
            ->set('form.precio', '10.00')
            ->call('save');

        $this->assertDatabaseHas('productos', [
            'nombre' => 'Producto Activo Default',
            'activo' => true,
        ]);
    }

    /**
     * El producto se guarda con el tipo correcto (producto o servicio).
     */
    public function test_el_producto_se_guarda_con_el_tipo_correcto(): void
    {
        $usuario = $this->usuarioConGestion();

        Livewire::actingAs($usuario)
            ->test(ProductoForm::class)
            ->set('form.nombre', 'Servicio de Instalación')
            ->set('form.precio', '99.00')
            ->set('form.tipo', 'servicio')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('productos', [
            'nombre' => 'Servicio de Instalación',
            'tipo' => 'servicio',
        ]);
    }

    /**
     * aplica_impuesto se guarda según el toggle del formulario.
     */
    public function test_aplica_impuesto_se_guarda_segun_el_toggle(): void
    {
        $usuario = $this->usuarioConGestion();

        Livewire::actingAs($usuario)
            ->test(ProductoForm::class)
            ->set('form.nombre', 'Producto Sin Impuesto')
            ->set('form.precio', '10.00')
            ->set('form.aplica_impuesto', false)
            ->call('save');

        $this->assertDatabaseHas('productos', [
            'nombre' => 'Producto Sin Impuesto',
            'aplica_impuesto' => false,
        ]);
    }

    // =====================================================================
    // Edición
    // =====================================================================

    /**
     * Al editar, los campos cargan los datos reales del producto.
     */
    public function test_al_editar_los_campos_cargan_los_datos_reales(): void
    {
        $usuario = $this->usuarioConGestion();
        $producto = Producto::factory()->create([
            'nombre' => 'Nombre Real',
            'codigo' => 'SKU-REAL',
            'precio' => '75.25',
        ]);

        Livewire::actingAs($usuario)
            ->test(ProductoForm::class, ['producto' => $producto])
            ->assertSet('form.nombre', 'Nombre Real')
            ->assertSet('form.codigo', 'SKU-REAL')
            ->assertSet('form.precio', '75.25');
    }

    /**
     * Un usuario con permiso puede editar un producto y los cambios se reflejan.
     */
    public function test_se_puede_editar_un_producto_y_se_reflejan_los_cambios(): void
    {
        $usuario = $this->usuarioConGestion();
        $producto = Producto::factory()->create(['nombre' => 'Antes']);

        Livewire::actingAs($usuario)
            ->test(ProductoForm::class, ['producto' => $producto])
            ->set('form.nombre', 'Después')
            ->set('form.precio', '150.00')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'nombre' => 'Después',
            'precio' => '150.00',
        ]);
    }

    // =====================================================================
    // Manejo de imagen
    // =====================================================================

    /**
     * Se puede subir una imagen al crear y el imagen_path se guarda.
     */
    public function test_se_puede_subir_una_imagen_al_crear_un_producto(): void
    {
        Storage::fake('public');

        $usuario = $this->usuarioConGestion();

        Livewire::actingAs($usuario)
            ->test(ProductoForm::class)
            ->set('form.nombre', 'Producto Con Imagen')
            ->set('form.precio', '10.00')
            ->set('imagen', UploadedFile::fake()->image('producto.jpg'))
            ->call('save')
            ->assertHasNoErrors();

        $producto = Producto::where('nombre', 'Producto Con Imagen')->first();

        $this->assertNotNull($producto->imagen_path);
        $this->assertTrue(Storage::disk('public')->exists($producto->imagen_path));
    }

    /**
     * Al reemplazar la imagen, la anterior se elimina del storage.
     */
    public function test_al_reemplazar_la_imagen_se_elimina_la_anterior(): void
    {
        Storage::fake('public');

        Storage::disk('public')->put('productos/anterior.jpg', 'contenido');

        $producto = Producto::factory()->create([
            'nombre' => 'Con Imagen',
            'imagen_path' => 'productos/anterior.jpg',
        ]);

        $usuario = $this->usuarioConGestion();

        Livewire::actingAs($usuario)
            ->test(ProductoForm::class, ['producto' => $producto])
            ->set('imagen', UploadedFile::fake()->image('nueva.jpg'))
            ->call('save')
            ->assertHasNoErrors();

        Storage::disk('public')->assertMissing('productos/anterior.jpg');
        Storage::disk('public')->assertExists($producto->refresh()->imagen_path);
    }

    /**
     * Un producto sin imagen no genera errores y imagen_path queda null.
     */
    public function test_sin_imagen_el_campo_imagen_path_queda_null(): void
    {
        $usuario = $this->usuarioConGestion();

        Livewire::actingAs($usuario)
            ->test(ProductoForm::class)
            ->set('form.nombre', 'Producto Sin Imagen')
            ->set('form.precio', '10.00')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('productos', [
            'nombre' => 'Producto Sin Imagen',
            'imagen_path' => null,
        ]);
    }

    // =====================================================================
    // Regla de negocio: producto inactivo
    // =====================================================================

    /**
     * El scope activos() no incluye productos inactivos.
     */
    public function test_el_scope_activos_no_incluye_productos_inactivos(): void
    {
        Producto::factory()->create(['nombre' => 'Activo Uno']);
        Producto::factory()->inactivo()->create(['nombre' => 'Inactivo Uno']);

        $activos = Producto::activos()->get();

        $this->assertCount(1, $activos);
        $this->assertSame('Activo Uno', $activos->first()->nombre);
    }

    /**
     * Un producto inactivo no aparece en el listado filtrado por activos.
     */
    public function test_un_producto_inactivo_no_aparece_en_el_listado_filtrado_por_activos(): void
    {
        $inactivo = Producto::factory()->inactivo()->create(['nombre' => 'Retirado']);

        Livewire::test(ProductoIndex::class)
            ->set('filtroEstado', 'Activo')
            ->assertDontSee($inactivo->nombre);
    }

    /**
     * "Eliminar" desactiva el producto: el registro sigue existiendo en la BD.
     */
    public function test_desactivar_un_producto_no_lo_elimina_fisicamente(): void
    {
        $usuario = $this->usuarioConGestion();
        $producto = Producto::factory()->create(['nombre' => 'A Desactivar']);

        Livewire::actingAs($usuario)
            ->test(ProductoIndex::class)
            ->call('toggleActivo', $producto->id);

        // Sigue existiendo, solo cambia su estado
        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'activo' => false,
        ]);
    }

    /**
     * Un usuario sin permiso no puede desactivar productos.
     */
    public function test_un_usuario_sin_permiso_no_puede_desactivar_productos(): void
    {
        $usuario = User::factory()->create();
        $producto = Producto::factory()->create();

        Livewire::actingAs($usuario)
            ->test(ProductoIndex::class)
            ->call('toggleActivo', $producto->id)
            ->assertForbidden();

        $this->assertDatabaseHas('productos', [
            'id' => $producto->id,
            'activo' => true,
        ]);
    }

    // =====================================================================
    // Helpers
    // =====================================================================

    /**
     * Usuario con los permisos para gestionar productos.
     */
    protected function usuarioConGestion(): User
    {
        $usuario = User::factory()->create();
        $usuario->givePermissionTo('productos.crear', 'productos.editar', 'productos.eliminar');

        return $usuario;
    }
}
