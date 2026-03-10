<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;

class AppointmentSeeder extends Seeder
{
    /**
     * Ejecutar el seeder - Crear 50 citas médicas de prueba.
     */
    public function run(): void
    {
        $patients = Patient::all();
        $doctors = Doctor::all();

        // Verificar que existan pacientes y doctores
        if ($patients->isEmpty() || $doctors->isEmpty()) {
            $this->command->warn('No se encontraron pacientes o doctores. Primero ejecute los seeders correspondientes.');
            return;
        }

        $statuses = ['Programado', 'Completado', 'Cancelado'];

        // Crear 50 citas de prueba
        for ($i = 0; $i < 50; $i++) {
            $startHour = rand(8, 16);
            $startMinute = [0, 30][rand(0, 1)];
            $durationMinutes = [30, 60, 90][rand(0, 2)];

            $startTime = sprintf('%02d:%02d', $startHour, $startMinute);
            $endMinutes = ($startHour * 60 + $startMinute + $durationMinutes);
            $endTime = sprintf('%02d:%02d', intdiv($endMinutes, 60), $endMinutes % 60);

            Appointment::create([
                'patient_id' => $patients->random()->id,
                'doctor_id' => $doctors->random()->id,
                'date' => now()->addDays(rand(-10, 30))->format('Y-m-d'),
                'start_time' => $startTime,
                'end_time' => $endTime,
                'status' => $statuses[rand(0, 2)],
            ]);
        }
    }
}
