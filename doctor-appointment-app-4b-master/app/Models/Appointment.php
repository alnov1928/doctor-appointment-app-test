<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'date',
        'start_time',
        'end_time',
        'status',
    ];

    // Relación: una cita pertenece a un paciente
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    // Relación: una cita pertenece a un doctor
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
