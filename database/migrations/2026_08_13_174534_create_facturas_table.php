<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('facturas', function (Blueprint $table) {
            $table->id();
            $table->string('numero_factura')->unique();
            $table->foreignId('cliente_id')->constrained()->onDelete('restrict');
            $table->foreignId('vendedor_id')->constrained('users')->onDelete('restrict');
            $table->date('fecha_emision');
            $table->date('fecha_vencimiento')->nullable();
            $table->decimal('subtotal', 12, 2);
            $table->decimal('descuento_porcentaje', 5, 2)->default(0);
            $table->decimal('descuento_total', 12, 2)->default(0);
            $table->decimal('impuesto', 12, 2);
            $table->decimal('total', 12, 2);
            $table->string('estado');
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facturas');
    }
};
