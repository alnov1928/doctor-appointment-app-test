<?php

namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Alvaro Novelo',
            'email' => 'alvaro1@gmail.com',
            'password' => bcrypt('12345678'),
            'id_number' => '123456789',
            'address' => 'Calle Falsa 123',
            'phone' => '5551234123'

        ])->assignRole('Doctor');
    }
}
