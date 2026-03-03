<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar la migración - Crear tabla de tickets de soporte.
     */
    public function up(): void
    {
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Usuario que crea el ticket
            $table->string('title');           // Título del problema
            $table->text('description');        // Descripción detallada
            $table->string('status')->default('abierto'); // Estado del ticket
            $table->timestamps();
        });
    }

    /**
     * Revertir la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
