<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Speciality;

class SpecialitySeeder extends Seeder
{
    public function run(): void
    {
        $specialties = [
            'Cardiología', 'Pediatría', 'Dermatología', 'Neurología',
            'Ginecología', 'Traumatología', 'Psiquiatría', 'Oftalmología',
            'Medicina General', 'Otorrinolaringología', 'Urología'
        ];

        foreach ($specialties as $speciality) {
            Speciality::create(['name' => $speciality]);
        }
    }
}