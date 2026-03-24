<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Speciality;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    /**
     * Mostrar la lista de citas médicas.
     */
    public function index()
    {
        return view('admin.appointments.index');
    }

    /**
     * Mostrar el formulario para crear una nueva cita.
     * Acepta query params para buscar disponibilidad.
     */
    public function create(Request $request)
    {
        $specialties = Speciality::all();
        $patients = Patient::with('user')->get();
        $availableDoctors = collect();
        $searched = false;

        // Si se proporcionaron parámetros de búsqueda, buscar disponibilidad
        if ($request->filled('date') && $request->filled('hour')) {
            $searched = true;
            $date = $request->date;
            $hour = (int) $request->hour;

            // Obtener el día de la semana (1=Lunes, 7=Domingo)
            $carbonDate = Carbon::parse($date);
            $dayOfWeek = $carbonDate->dayOfWeekIso; // 1=Monday, 7=Sunday

            // Generar los 4 slots de 15 minutos para la hora seleccionada
            $timeSlots = [];
            for ($m = 0; $m < 60; $m += 15) {
                $start = sprintf('%02d:%02d:00', $hour, $m);
                $timeSlots[] = $start;
            }

            // Buscar doctores con disponibilidad para ese día y hora
            $query = Doctor::with(['user', 'speciality', 'schedules'])
                ->whereHas('schedules', function ($q) use ($dayOfWeek, $timeSlots) {
                    $q->where('day', $dayOfWeek)
                      ->whereIn('start_time', $timeSlots);
                });

            // Filtrar por especialidad si se proporcionó
            if ($request->filled('speciality_id')) {
                $query->where('speciality_id', $request->speciality_id);
            }

            $doctors = $query->get();

            // Para cada doctor, obtener sus slots disponibles (sin conflicto con citas existentes)
            foreach ($doctors as $doctor) {
                // Slots configurados del doctor para ese día y hora
                $doctorSlots = $doctor->schedules
                    ->where('day', $dayOfWeek)
                    ->whereIn('start_time', $timeSlots)
                    ->values();

                // Citas existentes del doctor para esa fecha (no canceladas)
                $existingAppointments = Appointment::where('doctor_id', $doctor->id)
                    ->where('date', $date)
                    ->where('status', '!=', 'Cancelado')
                    ->get();

                // Filtrar slots que NO tienen conflicto
                $freeSlots = $doctorSlots->filter(function ($slot) use ($existingAppointments) {
                    foreach ($existingAppointments as $appointment) {
                        // Si el slot coincide exactamente con una cita existente, está ocupado
                        if ($slot->start_time === $appointment->start_time) {
                            return false;
                        }
                    }
                    return true;
                })->values();

                if ($freeSlots->isNotEmpty()) {
                    $doctor->freeSlots = $freeSlots;
                    $availableDoctors->push($doctor);
                }
            }
        }

        return view('admin.appointments.create', compact(
            'specialties', 'patients', 'availableDoctors', 'searched'
        ));
    }

    /**
     * Guardar una nueva cita en la base de datos.
     */
    public function store(Request $request, WhatsAppService $whatsAppService)
    {
        // Validar los datos del formulario
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'required|in:Programado,Completado,Cancelado',
        ]);

        // Verificar que no exista conflicto de horario (doble booking)
        $conflict = Appointment::where('doctor_id', $data['doctor_id'])
            ->where('date', $data['date'])
            ->where('start_time', $data['start_time'])
            ->where('status', '!=', 'Cancelado')
            ->exists();

        if ($conflict) {
            session()->flash('swal', [
                'icon' => 'error',
                'title' => '¡Conflicto de horario!',
                'text' => 'El doctor ya tiene una cita programada en ese horario. Por favor seleccione otro horario.',
            ]);
            return redirect()->back()->withInput();
        }

        // Crear la cita
        $appointment = Appointment::create($data);
        // ----------- NOTIFICACIONES DE WHATSAPP -----------
        // Cargar paciente y doctor para obtener sus datos
        $patient = Patient::with('user')->find($data['patient_id']);
        $doctor = Doctor::with('user')->find($data['doctor_id']);

        if ($patient && $patient->user->phone) {
            $formattedDate = Carbon::parse($data['date'])->translatedFormat('d \d\e F \d\e Y');
            $formattedTime = Carbon::parse($data['start_time'])->format('h:i A');

            // Mensaje de confirmación predeterminado
            $message = "🏥 *MediMatch - Cita Confirmada* \n\n"
                     . "Hola *{$patient->user->name}*,\n\n"
                     . "Tu cita ha sido agendada con éxito.\n"
                     . "👨‍⚕️ *Doctor(a):* {$doctor->user->name}\n"
                     . "📅 *Fecha:* {$formattedDate}\n"
                     . "⏰ *Hora:* {$formattedTime}\n\n"
                     . "Por favor procura llegar 10 minutos antes. ¡Te esperamos!";

            // Enviar mensaje a través del servicio implementado
            $whatsAppService->sendMessage($patient->user->phone, $message);
        }

        // Mostrar mensaje de éxito con SweetAlert
        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Cita creada!',
            'text' => 'La cita médica ha sido registrada exitosamente y el paciente notificado.',
        ]);

        return redirect()->route('admin.appointments.index');
    }

    /**
     * Mostrar el formulario para editar una cita existente.
     */
    public function edit(Appointment $appointment)
    {
        $patients = Patient::with('user')->get();
        $doctors = Doctor::with('user')->get();
        return view('admin.appointments.edit', compact('appointment', 'patients', 'doctors'));
    }

    /**
     * Actualizar una cita existente en la base de datos.
     */
    public function update(Request $request, Appointment $appointment)
    {
        // Validar los datos del formulario
        $data = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:doctors,id',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'status' => 'required|in:Programado,Completado,Cancelado',
        ]);

        // Verificar conflicto de horario (excluyendo la cita actual)
        $conflict = Appointment::where('doctor_id', $data['doctor_id'])
            ->where('date', $data['date'])
            ->where('start_time', $data['start_time'])
            ->where('status', '!=', 'Cancelado')
            ->where('id', '!=', $appointment->id)
            ->exists();

        if ($conflict) {
            session()->flash('swal', [
                'icon' => 'error',
                'title' => '¡Conflicto de horario!',
                'text' => 'El doctor ya tiene una cita programada en ese horario.',
            ]);
            return redirect()->back()->withInput();
        }

        // Actualizar la cita
        $appointment->update($data);

        // Mostrar mensaje de éxito con SweetAlert
        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Cita actualizada!',
            'text' => 'La cita médica ha sido actualizada exitosamente.',
        ]);

        return redirect()->route('admin.appointments.index');
    }

    /**
     * Eliminar una cita médica.
     */
    public function destroy(Appointment $appointment)
    {
        $appointment->delete();

        // Mostrar mensaje de éxito con SweetAlert
        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Cita eliminada!',
            'text' => 'La cita médica ha sido eliminada exitosamente.',
        ]);

        return redirect()->route('admin.appointments.index');
    }
}
