<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

test('authenticated user can create a new user', function () {
    // 1. Crear un usuario administrador para iniciar sesión
    // La factory ahora llenará id_number, phone, etc. automáticamente
    $admin = User::factory()->create();
    
    // 2. Crear un rol necesario (Paciente, Doctor, etc.)
    $role = Role::create(['name' => 'Paciente', 'guard_name' => 'web']);

    // 3. Datos para el NUEVO usuario
    $userData = [
        'name' => 'Nuevo Usuario Test',
        'email' => 'test_create@medimatch.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'id_number' => '99999999',
        'phone' => '5551234567',
        'address' => 'Calle Pruebas 123',
        'role_id' => $role->id,
    ];

    // 4. Actuar: El admin envía el formulario POST
    $response = $this->actingAs($admin)
        ->post(route('admin.users.store'), $userData);

    // 5. Verificar: Redirección y existencia en BD
    $response->assertRedirect(route('admin.users.index'));
    $response->assertSessionHasNoErrors();
    
    $this->assertDatabaseHas('users', [
        'email' => 'test_create@medimatch.com',
        'id_number' => '99999999',
    ]);
});