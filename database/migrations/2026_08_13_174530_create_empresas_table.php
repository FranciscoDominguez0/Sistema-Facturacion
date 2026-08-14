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
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('identificacion_fiscal')->nullable();
            $table->string('logo_path')->nullable();
            $table->string('moneda');
            $table->string('simbolo_moneda');
            $table->string('impuesto_nombre')->nullable();
            $table->decimal('impuesto_porcentaje', 5, 2);
            $table->string('prefijo_factura');
            $table->integer('siguiente_numero_factura');
            $table->string('color_primario');
            $table->text('pie_pagina_pdf')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('empresas');
    }
};
