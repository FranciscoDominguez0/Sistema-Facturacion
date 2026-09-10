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
        Schema::table('productos', function (Blueprint $table) {
            $table->foreignId('impuesto_id')->nullable()->constrained('impuestos')->onDelete('set null');
            $table->dropColumn('aplica_impuesto');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('productos', function (Blueprint $table) {
            $table->boolean('aplica_impuesto')->default(true);
            $table->dropForeign(['impuesto_id']);
            $table->dropColumn('impuesto_id');
        });
    }
};
