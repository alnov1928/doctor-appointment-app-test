<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Models\User;
use App\Models\Doctor;
use App\Mail\DailyAppointmentsReportMail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendDailyAppointmentsReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:daily-appointments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía el reporte diario de citas al administrador y a cada doctor.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $today = Carbon::today()->format('Y-m-d');
        
        $this->info("Iniciando generación de reportes de citas para el día: {$today}");

        // Obtener todas las citas de HOY ordenadas por hora
        $todaysAppointments = Appointment::with(['patient.user', 'doctor.user'])
            ->whereDate('date', $today)
            ->orderBy('start_time', 'asc')
            ->get();

        // 1. Enviar el reporte general al Administrador del sistema
        $adminEmail = env('MAIL_FROM_ADDRESS'); // Enviamos al correo principal del sistema configurado en .env
        
        if ($adminEmail) {
            $this->info("Enviando reporte general al Administrador ({$adminEmail})...");
            Mail::to($adminEmail)->send(new DailyAppointmentsReportMail($todaysAppointments, 'admin'));
            $this->info("Reporte general enviado con éxito.");
        }

        // 2. Agrupar las citas por doctor y enviarles sus reportes individuales
        $appointmentsByDoctor = $todaysAppointments->groupBy('doctor_id');

        if ($appointmentsByDoctor->isNotEmpty()) {
            foreach ($appointmentsByDoctor as $doctorId => $doctorAppointments) {
                $doctor = Doctor::with('user')->find($doctorId);
                
                if ($doctor && $doctor->user && $doctor->user->email) {
                    $this->info("Enviando reporte individual al Doctor {$doctor->user->name} ({$doctor->user->email})...");
                    Mail::to($doctor->user->email)->send(new DailyAppointmentsReportMail($doctorAppointments, 'doctor'));
                }
            }
            $this->info("Reportes individuales enviados con éxito a doctores.");
        } else {
            $this->info("No hay citas registradas para los doctores el día de hoy.");
        }

        $this->info("Proceso de reportes matutinos finalizado.");
    }
}
