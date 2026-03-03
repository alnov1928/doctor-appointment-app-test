<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

//Usamos RefreshDatabase para asegurar que la base de datos se reinicie en cada prueba
uses(RefreshDatabase::class);



test('Un usuario no puede eliminar su propia cuenta', function () {
    //Crear un usuario de prueba
    $user = User::factory()->create();

    //Simular que el usuario ha iniciado sesión
    $this->actingAs($user, 'web');

    //Simulamos una solicitud DELETE para eliminar la cuenta del usuario autenticado
    $response = $this->delete(route('admin.users.destroy', $user));

    //Esperamos que el servidor bloqueo la opcion y redirija al usuario
    $response->assertStatus(403);

    //Verificar que el usuario todavía existe en la base de datos
    $this->assertDatabaseHas('users', ['id' => $user->id]);
});
