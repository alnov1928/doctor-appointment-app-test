<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    /**
     * Mostrar el gestor de horarios.
     */
    public function index(Request $request)
    {
        $doctors = Doctor::with('user')->get();
        $selectedDoctor = null;
        $existingSlots = [];

        // Si se seleccionó un doctor, obtener sus horarios existentes
        if ($request->has('doctor_id') && $request->doctor_id) {
            $selectedDoctor = Doctor::with('user')->findOrFail($request->doctor_id);
            
            // Obtener los slots existentes como un array simple para comparación en la vista
            $existingSlots = DoctorSchedule::where('doctor_id', $selectedDoctor->id)
                ->get()
                ->map(function ($schedule) {
                    return $schedule->day . '_' . $schedule->start_time;
                })
                ->toArray();
        }

        return view('admin.schedules.index', compact('doctors', 'selectedDoctor', 'existingSlots'));
    }

    /**
     * Guardar los horarios del doctor.
     */
    public function store(Request $request)
    {
        $request->validate([
            'doctor_id' => 'required|exists:doctors,id',
            'slots' => 'nullable|array',
            'slots.*' => 'string',
        ]);

        $doctorId = $request->doctor_id;

        // Eliminar todos los horarios anteriores del doctor
        DoctorSchedule::where('doctor_id', $doctorId)->delete();

        // Crear los nuevos horarios seleccionados
        if ($request->has('slots')) {
            foreach ($request->slots as $slot) {
                // Formato del slot: "day_startTime" (ej: "1_08:00")
                $parts = explode('_', $slot, 2);
                if (count($parts) === 2) {
                    $day = (int) $parts[0];
                    $startTime = $parts[1];
                    
                    // Calcular end_time sumando 15 minutos
                    $endTime = date('H:i', strtotime($startTime . ' +15 minutes'));

                    DoctorSchedule::create([
                        'doctor_id' => $doctorId,
                        'day' => $day,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                    ]);
                }
            }
        }

        // Mostrar mensaje de éxito con SweetAlert
        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Horario guardado!',
            'text' => 'La disponibilidad del doctor ha sido actualizada exitosamente.',
        ]);

        return redirect()->route('admin.schedules.index', ['doctor_id' => $doctorId]);
    }
}
