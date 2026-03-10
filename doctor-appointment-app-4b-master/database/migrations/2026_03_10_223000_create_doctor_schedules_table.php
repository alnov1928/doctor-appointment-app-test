<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar la migración - Crear tabla de horarios de doctores.
     */
    public function up(): void
    {
        Schema::create('doctor_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('doctor_id')->constrained()->onDelete('cascade'); // Doctor al que pertenece el horario
            $table->tinyInteger('day');        // Día de la semana (1=Lunes, 2=Martes, ..., 7=Domingo)
            $table->time('start_time');        // Hora de inicio del slot
            $table->time('end_time');          // Hora de fin del slot
            $table->timestamps();

            // Índice único para evitar duplicados
            $table->unique(['doctor_id', 'day', 'start_time']);
        });
    }

    /**
     * Revertir la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_schedules');
    }
};
