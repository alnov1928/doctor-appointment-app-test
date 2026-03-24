<?php

namespace App\Console\Commands;

use App\Models\Appointment;
use App\Services\WhatsAppService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendAppointmentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'appointments:reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envía recordatorios de WhatsApp para citas programadas para el día de mañana';

    /**
     * Execute the console command.
     */
    public function handle(WhatsAppService $whatsAppService)
    {
        $this->info('Iniciando envío de recordatorios de citas...');

        // Obtener la fecha de mañana
        $tomorrow = Carbon::tomorrow()->format('Y-m-d');

        // Buscar citas programadas para mañana
        $appointments = Appointment::with(['patient.user', 'doctor.user'])
            ->where('date', $tomorrow)
            ->where('status', 'Programado')
            ->get();

        if ($appointments->isEmpty()) {
            $this->info('No hay citas programadas para mañana.');
            return;
        }

        $count = 0;

        foreach ($appointments as $appointment) {
            $patient = $appointment->patient;
            $doctor = $appointment->doctor;

            if ($patient && $patient->user->phone) {
                $formattedDate = Carbon::parse($appointment->date)->translatedFormat('d \d\e F \d\e Y');
                $formattedTime = Carbon::parse($appointment->start_time)->format('h:i A');

                $message = "⏳ *MediMatch - Recordatorio de Cita* \n\n"
                         . "Hola *{$patient->user->name}*,\n\n"
                         . "Te recordamos que tienes una cita programada para el día de mañana.\n"
                         . "👨‍⚕️ *Doctor(a):* {$doctor->user->name}\n"
                         . "📅 *Fecha:* {$formattedDate}\n"
                         . "⏰ *Hora:* {$formattedTime}\n\n"
                         . "Por favor procura llegar 10 minutos antes. Si no puedes asistir, contáctanos para reagendar.";
                // Enviar mensaje
                $success = $whatsAppService->sendMessage($patient->user->phone, $message);

                if ($success) {
                    $this->info("Recordatorio enviado a {$patient->user->name} ({$patient->user->phone}).");
                    $count++;
                } else {
                    $this->error("Falló el envío a {$patient->user->name} ({$patient->user->phone}).");
                }
            }
        }
        $this->info("Proceso finalizado. $count recordatorios enviados.");
    }
}
