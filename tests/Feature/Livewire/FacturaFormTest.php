<?php

namespace Tests\Feature\Livewire;

use App\Livewire\Facturas\FacturaForm;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Factura;
use App\Models\Impuesto;
use App\Models\Producto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

/**
 * Tests del formulario de nueva venta (FacturaForm).
 *
 * El proyecto usa PHPUnit clásico con nombres de test en español.
 */
class FacturaFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Permisos del módulo de facturas
        Permission::findOrCreate('facturas.ver');
        Permission::findOrCreate('facturas.crear');
        Permission::findOrCreate('facturas.estado.cambiar');
        Permission::findOrCreate('facturas.vendedor.seleccionar');
        Permission::findOrCreate('facturas.descuento');
    }

    // =====================================================================
    // Acceso al formulario
    // =====================================================================

    /**
     * Un usuario autenticado con permiso puede ver el formulario de nueva venta.
     */
    public function test_un_usuario_con_permiso_puede_ver_el_formulario_de_nueva_venta(): void
    {
        $usuario = User::factory()->create();
        $usuario->givePermissionTo('facturas.crear');

        $this->actingAs($usuario)
            ->get(route('facturas.crear'))
            ->assertOk();
    }

    /**
     * Un usuario autenticado sin permiso recibe 403 al intentar acceder.
     */
    public function test_un_usuario_sin_permiso_no_puede_acceder_al_formulario_de_nueva_venta(): void
    {
        $usuario = User::factory()->create();

        $this->actingAs($usuario)
            ->get(route('facturas.crear'))
            ->assertForbidden();
    }

    // =====================================================================
    // Selección de cliente
    // =====================================================================

    /**
     * El select de clientes recibe todos los clientes activos como opciones;
     * el filtrado por nombre ocurre en el cliente dentro del componente Alpine.
     */
    public function test_el_select_de_clientes_incluye_a_todos_los_clientes_activos(): void
    {
        Cliente::factory()->create(['nombre' => 'María Gómez']);
        Cliente::factory()->create(['nombre' => 'Carlos Pérez']);
        $usuario = User::factory()->create();

        Livewire::actingAs($usuario)
            ->test(FacturaForm::class)
            ->assertViewHas('clientes', function (array $clientes): bool {
                return count($clientes) === 2
                    && $clientes[0]['nombre'] === 'María Gómez'
                    && $clientes[1]['nombre'] === 'Carlos Pérez';
            });
    }

    /**
     * Se puede crear un cliente nuevo solo con nombre y queda seleccionado
     * automáticamente en el formulario, sin recargar la página.
     */
    public function test_se_puede_crear_un_cliente_express_y_queda_seleccionado(): void
    {
        $usuario = User::factory()->create();

        $componente = Livewire::actingAs($usuario)
            ->test(FacturaForm::class)
            ->set('nuevo_cliente_nombre', 'Cliente Express')
            ->call('guardarClienteExpress')
            ->assertHasNoErrors();

        $cliente = Cliente::where('nombre', 'Cliente Express')->firstOrFail();

        // El cliente queda seleccionado automáticamente en el formulario
        $this->assertSame($cliente->id, $componente->get('form.cliente_id'));
        $this->assertSame($cliente->id, $componente->get('cliente_id'));

        $this->assertDatabaseHas('clientes', [
            'id' => $cliente->id,
            'nombre' => 'Cliente Express',
            'activo' => true,
        ]);
    }

    /**
     * Crear un cliente sin nombre falla la validación.
     */
    public function test_crear_cliente_sin_nombre_falla_la_validacion(): void
    {
        $usuario = User::factory()->create();

        Livewire::actingAs($usuario)
            ->test(FacturaForm::class)
            ->set('nuevo_cliente_nombre', '')
            ->call('guardarClienteExpress')
            ->assertHasErrors(['nuevo_cliente_nombre' => 'required']);

        $this->assertDatabaseCount('clientes', 0);
    }

    // =====================================================================
    // Líneas de venta
    // =====================================================================

    /**
     * Agregar línea añade una fila vacía al array de items.
     */
    public function test_agregar_linea_anade_una_fila_vacia(): void
    {
        $usuario = User::factory()->create();

        $componente = Livewire::actingAs($usuario)->test(FacturaForm::class);

        // El formulario inicia con una línea
        $this->assertCount(1, $componente->get('form.items'));

        $componente->call('agregarLinea');

        $this->assertCount(2, $componente->get('form.items'));
        $this->assertSame(0, $componente->get('form.items.1.subtotal_linea'));
    }

    /**
     * Eliminar línea remueve correctamente la fila del array.
     */
    public function test_eliminar_linea_remueve_la_fila_del_array(): void
    {
        $usuario = User::factory()->create();

        $componente = Livewire::actingAs($usuario)->test(FacturaForm::class);
        $componente->call('agregarLinea');
        $this->assertCount(2, $componente->get('form.items'));

        $componente->call('eliminarLinea', 0);

        $this->assertCount(1, $componente->get('form.items'));
        // El array se reindexa
        $this->assertSame(0.0, $componente->get('form.items.0.subtotal_linea'));
    }

    /**
     * Cambiar cantidad o precio_unitario recalcula el subtotal_linea y los
     * totales generales en tiempo real.
     */
    public function test_cambiar_cantidad_y_precio_recalcula_los_totales(): void
    {
        Empresa::factory()->create(['impuesto_porcentaje' => 7]);
        $usuario = User::factory()->create();

        Livewire::actingAs($usuario)
            ->test(FacturaForm::class)
            ->set('form.items.0.descripcion', 'Producto A')
            ->set('form.items.0.impuesto_porcentaje', 7)
            ->set('form.items.0.cantidad', 2)
            ->set('form.items.0.precio_unitario', 100)
            ->assertSet('form.items.0.subtotal_linea', 200.0)
            ->assertSet('form.subtotal', 200.0)
            ->assertSet('form.impuesto', 14.0)
            ->assertSet('form.total', 214.0);
    }

    /**
     * Seleccionar un producto del catálogo completa el precio y el impuesto,
     * pero NO la descripción: el usuario la escribe manualmente.
     */
    public function test_seleccionar_producto_no_autocompleta_la_descripcion(): void
    {
        $producto = Producto::factory()->create(['nombre' => 'Laptop Pro', 'precio' => 150.50]);
        $usuario = User::factory()->create();

        Livewire::actingAs($usuario)
            ->test(FacturaForm::class)
            ->set('form.items.0.producto_id', $producto->id)
            ->assertSet('form.items.0.producto_id', $producto->id)
            ->assertSet('form.items.0.descripcion', '')
            ->assertSet('form.items.0.precio_unitario', '150.50');

        // La descripción se puede escribir sin problema
        Livewire::actingAs($usuario)
            ->test(FacturaForm::class)
            ->set('form.items.0.producto_id', $producto->id)
            ->set('form.items.0.descripcion', 'Laptop Pro 16GB RAM')
            ->assertSet('form.items.0.descripcion', 'Laptop Pro 16GB RAM');
    }

    /**
     * Al seleccionar un producto se copian su impuesto y, si el usuario cambia
     * el impuesto de la línea, el cálculo y lo guardado usan el nuevo valor.
     */
    public function test_cambiar_el_impuesto_de_la_linea_recalcula_y_guarda_el_nuevo_porcentaje(): void
    {
        Empresa::factory()->create(['impuesto_porcentaje' => 7]);
        $cliente = Cliente::factory()->create();
        [$usuario] = $this->usuarioConVendedor(['facturas.crear']);

        $impuesto7 = Impuesto::create(['nombre' => 'ITBMS', 'porcentaje' => 7, 'activo' => true]);
        $impuesto10 = Impuesto::create(['nombre' => 'Impuesto Especial', 'porcentaje' => 10, 'activo' => true]);
        $producto = Producto::factory()->create([
            'nombre' => 'Laptop Pro',
            'precio' => 150.50,
            'impuesto_id' => $impuesto7->id,
        ]);

        Livewire::actingAs($usuario)
            ->test(FacturaForm::class)
            // Seleccionar el producto completa su impuesto (7%)
            ->set('form.items.0.producto_id', $producto->id)
            ->assertSet('form.items.0.impuesto_id', $impuesto7->id)
            ->assertSet('form.items.0.impuesto_nombre', 'ITBMS')
            ->assertSet('form.items.0.impuesto_porcentaje', 7)
            // Cambiar el impuesto de la línea a 10% recalcula todo
            ->set('form.items.0.impuesto_id', $impuesto10->id)
            ->assertSet('form.items.0.impuesto_nombre', 'Impuesto Especial')
            ->assertSet('form.items.0.impuesto_porcentaje', 10)
            ->assertSet('form.items.0.impuesto_monto', 15.05)
            ->assertSet('form.impuesto', 15.05)
            ->set('form.cliente_id', $cliente->id)
            ->call('save')
            ->assertHasNoErrors();

        // Lo guardado usa el impuesto cambiado, no el del producto
        $this->assertDatabaseHas('facturas', ['impuesto' => 15.05]);
        $this->assertDatabaseHas('factura_items', [
            'impuesto_id' => $impuesto10->id,
            'impuesto_nombre' => 'Impuesto Especial',
            'impuesto_porcentaje' => 10,
            'impuesto_monto' => 15.05,
        ]);
    }

    /**
     * La descripción no es obligatoria: si la línea queda sin descripción y
     * tiene producto, al guardar se usa el nombre (o descripción) del producto.
     */
    public function test_guardar_sin_descripcion_usa_la_del_producto(): void
    {
        Empresa::factory()->create(['impuesto_porcentaje' => 7]);
        $cliente = Cliente::factory()->create();
        [$usuario, $vendedor] = $this->usuarioConVendedor(['facturas.crear']);

        $producto = Producto::factory()->create(['nombre' => 'Laptop Pro', 'descripcion' => null]);

        Livewire::actingAs($usuario)
            ->test(FacturaForm::class)
            ->set('form.cliente_id', $cliente->id)
            ->set('form.vendedor_id', $vendedor->id)
            ->set('form.items.0.producto_id', $producto->id)
            ->set('form.items.0.cantidad', 1)
            ->set('form.items.0.precio_unitario', 100)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('factura_items', [
            'descripcion' => 'Laptop Pro',
            'producto_id' => $producto->id,
        ]);
    }

    // =====================================================================
    // Descuentos
    // =====================================================================

    /**
     * Un usuario sin permiso de descuento no puede aplicar descuentos por
     * línea: los descuentos enviados se ignoran al guardar.
     */
    public function test_usuario_sin_permiso_de_descuento_no_puede_aplicar_descuentos(): void
    {
        Empresa::factory()->create(['impuesto_porcentaje' => 7]);
        $cliente = Cliente::factory()->create();
        [$usuario, $vendedor] = $this->usuarioConVendedor(['facturas.crear']);

        Livewire::actingAs($usuario)
            ->test(FacturaForm::class)
            ->set('form.cliente_id', $cliente->id)
            ->set('form.items.0.descripcion', 'Producto A')
            ->set('form.items.0.cantidad', 1)
            ->set('form.items.0.precio_unitario', 100)
            ->set('form.items.0.descuento_porcentaje', 20)
            ->call('save')
            ->assertHasNoErrors();

        // El descuento enviado se ignoró: la línea y la factura quedan sin descuento
        $this->assertDatabaseHas('factura_items', [
            'descripcion' => 'Producto A',
            'descuento_porcentaje' => 0,
            'descuento_monto' => 0,
        ]);
        $this->assertDatabaseHas('facturas', [
            'cliente_id' => $cliente->id,
            'vendedor_id' => $vendedor->id,
            'descuento_total' => 0,
        ]);
    }

    // =====================================================================
    // Guardado
    // =====================================================================

    /**
     * Guardar la venta sin cliente falla la validación.
     */
    public function test_guardar_sin_cliente_falla_la_validacion(): void
    {
        Empresa::factory()->create(['impuesto_porcentaje' => 7]);
        [$usuario] = $this->usuarioConVendedor(['facturas.crear']);

        Livewire::actingAs($usuario)
            ->test(FacturaForm::class)
            ->set('form.items.0.descripcion', 'Producto A')
            ->set('form.items.0.cantidad', 1)
            ->set('form.items.0.precio_unitario', 100)
            ->call('save')
            ->assertHasErrors(['form.cliente_id' => 'required']);

        $this->assertDatabaseCount('facturas', 0);
    }

    /**
     * Guardar la venta sin ninguna línea válida falla la validación.
     */
    public function test_guardar_sin_lineas_validas_falla_la_validacion(): void
    {
        Empresa::factory()->create(['impuesto_porcentaje' => 7]);
        $cliente = Cliente::factory()->create();
        [$usuario] = $this->usuarioConVendedor(['facturas.crear']);

        Livewire::actingAs($usuario)
            ->test(FacturaForm::class)
            ->set('form.cliente_id', $cliente->id)
            ->call('eliminarLinea', 0)
            ->call('save')
            ->assertHasErrors(['form.items' => 'required']);

        $this->assertDatabaseCount('facturas', 0);
    }

    /**
     * Guardar una venta válida crea la factura con su número correlativo,
     * crea los factura_items asociados y redirige al detalle.
     */
    public function test_guardar_venta_exitosa_crea_la_factura_y_redirige(): void
    {
        Empresa::factory()->create(['impuesto_porcentaje' => 7]);
        $cliente = Cliente::factory()->create();
        [$usuario, $vendedor] = $this->usuarioConVendedor(['facturas.crear']);

        $componente = Livewire::actingAs($usuario)
            ->test(FacturaForm::class)
            ->set('form.cliente_id', $cliente->id)
            ->set('form.items.0.descripcion', 'Producto A')
            ->set('form.items.0.cantidad', 2)
            ->set('form.items.0.precio_unitario', 100)
            ->set('form.items.0.impuesto_porcentaje', 7)
            ->call('save')
            ->assertHasNoErrors();

        $factura = Factura::firstOrFail();

        $componente->assertRedirect(route('facturas.show', $factura->id));

        $this->assertDatabaseHas('facturas', [
            'id' => $factura->id,
            'numero_factura' => 'FAC-000001',
            'cliente_id' => $cliente->id,
            'vendedor_id' => $vendedor->id,
            'estado' => 'Pendiente',
            'subtotal' => 200,
            'impuesto' => 14,
            'total' => 214,
        ]);

        $this->assertDatabaseCount('factura_items', 1);
        $this->assertDatabaseHas('factura_items', [
            'factura_id' => $factura->id,
            'descripcion' => 'Producto A',
            'cantidad' => 2,
            'precio_unitario' => 100,
        ]);
    }

    // =====================================================================
    // Vendedor y fechas
    // =====================================================================

    /**
     * El vendedor se autoasigna al usuario autenticado si no tiene permiso
     * de selección.
     */
    public function test_el_vendedor_se_autoasigna_sin_permiso_de_seleccion(): void
    {
        [$usuario, $vendedor] = $this->usuarioConVendedor(['facturas.crear']);

        Livewire::actingAs($usuario)
            ->test(FacturaForm::class)
            ->assertSet('form.vendedor_id', $vendedor->id);
    }

    /**
     * El vendedor se puede elegir manualmente si el usuario tiene el permiso
     * de selección.
     */
    public function test_el_usuario_con_permiso_de_seleccion_puede_elegir_vendedor(): void
    {
        $usuario = User::factory()->create();
        $usuario->givePermissionTo('facturas.vendedor.seleccionar');
        Role::findOrCreate('Vendedor');
        $otroVendedor = User::factory()->create();
        $otroVendedor->assignRole('Vendedor');

        Livewire::actingAs($usuario)
            ->test(FacturaForm::class)
            ->assertSet('form.vendedor_id', null)
            ->set('form.vendedor_id', $otroVendedor->id)
            ->assertSet('form.vendedor_id', $otroVendedor->id);
    }

    /**
     * La fecha de emisión por defecto es la fecha actual si no se modifica.
     */
    public function test_la_fecha_de_emision_por_defecto_es_hoy(): void
    {
        $usuario = User::factory()->create();

        Livewire::actingAs($usuario)
            ->test(FacturaForm::class)
            ->assertSet('form.fecha_emision', date('Y-m-d'));
    }

    // =====================================================================
    // Helpers
    // =====================================================================

    /**
     * Usuario con el rol vendedor asociado y los permisos indicados.
     *
     * @return array{0: User, 1: User}
     */
    protected function usuarioConVendedor(array $permisos = []): array
    {
        Role::findOrCreate('Vendedor');
        $usuario = User::factory()->create();
        $usuario->assignRole('Vendedor');

        $usuario->givePermissionTo($permisos);

        return [$usuario, $usuario];
    }
}
