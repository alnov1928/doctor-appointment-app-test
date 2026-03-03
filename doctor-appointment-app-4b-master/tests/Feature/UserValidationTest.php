<?php

use App\Models\User;
use Spatie\Permission\Models\Role;

test('cannot create a user with a duplicate email', function () {
    // 1. Crear admin y rol
    $admin = User::factory()->create();
    $role = Role::create(['name' => 'Doctor', 'guard_name' => 'web']);

    // 2. Crear un usuario existente con un email específico
    User::factory()->create([
        'email' => 'duplicado@test.com'
    ]);

    // 3. Intentar crear OTRO usuario con el MISMO email
    $response = $this->actingAs($admin)
        ->post(route('admin.users.store'), [
            'name' => 'Usuario Intruso',
            'email' => 'duplicado@test.com', // <--- Email repetido
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'id_number' => '11112222',
            'phone' => '5555555555',
            'address' => 'Calle Falsa',
            'role_id' => $role->id,
        ]);

    // 4. Verificar que la sesión tenga error en el campo 'email'
    $response->assertSessionHasErrors(['email']);
    
    // 5. Opcional: Verificar que no se creó el usuario intruso
    $this->assertDatabaseMissing('users', [
        'name' => 'Usuario Intruso'
    ]);
});