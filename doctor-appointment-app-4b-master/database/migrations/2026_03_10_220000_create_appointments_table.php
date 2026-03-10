<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar la migración - Crear tabla de citas médicas.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');  // Paciente de la cita
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade');   // Doctor asignado
            $table->date('date');                                                  // Fecha de la cita
            $table->time('start_time');                                            // Hora de inicio
            $table->time('end_time');                                              // Hora de fin
            $table->string('status')->default('Programado');                       // Estado: Programado, Completado, Cancelado
            $table->timestamps();
        });
    }

    /**
     * Revertir la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
