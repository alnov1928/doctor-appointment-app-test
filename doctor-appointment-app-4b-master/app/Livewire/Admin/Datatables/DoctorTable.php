<?php

namespace App\Livewire\Admin\Datatables;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use App\Models\Doctor;

class DoctorTable extends DataTableComponent
{
    protected $model = Doctor::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        return [
            Column::make("ID", "id")
                ->sortable(),

            
            Column::make("Doctor", "user.name")
                ->sortable()
                ->searchable(),

            
            Column::make("Especialidad", "speciality.name")
                ->sortable()
                ->searchable()
                ->format(fn($value) => $value ? $value : 'No asignada'),

            
            Column::make("Cédula Profesional", "professional_license")
                ->sortable()
                ->searchable()
                ->format(fn($value) => $value ? $value : 'N/A'),

           
            Column::make("Biografía", "biography")
                ->format(fn($value) => $value ? $value : 'N/A'),

            
            Column::make("Acciones", "id")
                ->format(fn($value, $row) => view('admin.doctors.actions', ['doctor' => $row]))
                ->html(),
        ];
    }
}
