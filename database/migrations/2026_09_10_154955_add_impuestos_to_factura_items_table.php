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
        Schema::table('factura_items', function (Blueprint $table) {
            $table->foreignId('impuesto_id')->nullable()->constrained('impuestos')->onDelete('set null');
            $table->string('impuesto_nombre')->nullable();
            $table->decimal('impuesto_porcentaje', 5, 2)->nullable();
            $table->decimal('impuesto_monto', 12, 2)->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('factura_items', function (Blueprint $table) {
            $table->dropForeign(['impuesto_id']);
            $table->dropColumn(['impuesto_id', 'impuesto_nombre', 'impuesto_porcentaje', 'impuesto_monto']);
        });
    }
};
