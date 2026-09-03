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
        Schema::table('empresas', function (Blueprint $table) {
            $table->integer('password_length')->default(10);
            $table->boolean('password_special_char')->default(true);
            $table->boolean('password_mixed_case')->default(true);
            $table->integer('session_timeout')->default(30);
            $table->boolean('session_close_others')->default(true);
            $table->boolean('login_lockout')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('empresas', function (Blueprint $table) {
            $table->dropColumn([
                'password_length',
                'password_special_char',
                'password_mixed_case',
                'session_timeout',
                'session_close_others',
                'login_lockout'
            ]);
        });
    }
};
