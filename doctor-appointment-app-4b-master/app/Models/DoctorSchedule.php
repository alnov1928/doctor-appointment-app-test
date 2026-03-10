<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorSchedule extends Model
{
    // Campos que se pueden asignar masivamente
    protected $fillable = [
        'doctor_id',
        'day',
        'start_time',
        'end_time',
    ];

    // Relación: un horario pertenece a un doctor
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
