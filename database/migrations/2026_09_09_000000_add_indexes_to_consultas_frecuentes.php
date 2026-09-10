<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Índices para las consultas de listados y del dashboard, que filtran
     * y agrupan por estas columnas constantemente.
     */
    public function up(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->index('fecha_emision');
            $table->index('estado');
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->index('created_at');
        });

        Schema::table('gastos', function (Blueprint $table) {
            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::table('facturas', function (Blueprint $table) {
            $table->dropIndex(['fecha_emision']);
            $table->dropIndex(['estado']);
        });

        Schema::table('clientes', function (Blueprint $table) {
            $table->dropIndex(['created_at']);
        });

        Schema::table('gastos', function (Blueprint $table) {
            $table->dropIndex(['fecha']);
        });
    }
};