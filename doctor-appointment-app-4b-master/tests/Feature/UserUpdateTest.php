<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

test('authenticated user can update another user', function () {
    // 1. Crear admin y rol
    $admin = User::factory()->create();
    $role = Role::create(['name' => 'Recepcionista', 'guard_name' => 'web']);
    
    // 2. Crear el usuario que vamos a editar
    $userToEdit = User::factory()->create();

    // 3. Nuevos datos para actualizar
    // ADAPTACIÓN 1: Incluimos la contraseña porque tu controlador la exige ('required')
    $updatedData = [
        'name' => 'Nombre Actualizado',
        'email' => $userToEdit->email,       
        'id_number' => $userToEdit->id_number,
        'phone' => '9998887777',             
        'address' => 'Nueva Dirección #500', 
        'role_id' => $role->id,
        'password' => 'password123', // <--- Agregado
        'password_confirmation' => 'password123', // <--- Agregado
    ];

    // 4. Actuar: Petición PUT
    $response = $this->actingAs($admin)
        ->put(route('admin.users.update', $userToEdit), $updatedData);

    // 5. Verificar
    // ADAPTACIÓN 2: Esperamos que nos regrese a la vista 'edit', no al index
    $response->assertRedirect(route('admin.users.edit', $userToEdit->id));
    
    $response->assertSessionHasNoErrors();

    // Confirmar cambios en la base de datos
    $this->assertDatabaseHas('users', [
        'id' => $userToEdit->id,
        'name' => 'Nombre Actualizado',
        'address' => 'Nueva Dirección #500',
    ]);
});