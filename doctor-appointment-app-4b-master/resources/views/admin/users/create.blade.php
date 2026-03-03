<x-admin-layout 
    title="Crear Usuario | MediMatch" 
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],
        [
            'name' => 'Usuarios',
            'href' => route('admin.users.index'),
        ],
        [
            'name' => 'Nuevo',
        ],
    ]">

    <x-wire-card>
        <form action="{{ route('admin.users.store') }}" method="POST">
            @csrf

            {{-- Usamos GRID para crear las 2 columnas como en la imagen --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                
                {{-- Fila 1: Nombre y Email --}}
                <x-wire-input name="name" label="Nombre" placeholder="Nombre" :value="old('name')" required />
                <x-wire-input type="email" name="email" label="Email" placeholder="usuario@email.com" :value="old('email')" required />
                
                {{-- Fila 2: Contraseñas --}}
                <x-wire-input type="password" name="password" label="Contraseña" placeholder="Mínimo 8 caracteres" required />
                <x-wire-input type="password" name="password_confirmation" label="Confirmar contraseña" placeholder="Repita la contraseña" required />
                
                {{-- Fila 3: ID y Teléfono --}}
                <x-wire-input name="id_number" label="Número de ID" placeholder="Ej. 12345678" :value="old('id_number')" required />
                <x-wire-input name="phone" label="Teléfono" placeholder="Ej. 1234567890" :value="old('phone')" required />
                
                {{-- Fila 4: Dirección (Ocupa las 2 columnas 'col-span-2') --}}
                <div class="col-span-1 md:col-span-2">
                    <x-wire-input name="address" label="Dirección" placeholder="Ej. Calle 123" :value="old('address')" required />
                </div>

                {{-- Fila 5: Rol (Ocupa las 2 columnas) --}}
                <div class="col-span-1 md:col-span-2">
                    <x-wire-native-select name="role_id" label="Rol" placeholder="Seleccione un rol" required>
                        <option value="">Seleccione un rol</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" @selected(old('role_id') == $role->id)>
                                {{ $role->name }}
                            </option>
                        @endforeach
                    </x-wire-native-select>
                    <p class="text-sm text-gray-500 mt-1">
                        Define los permisos y accesos del usuario en el sistema.
                    </p>
                </div>

            </div>
            
            {{-- Botón de Guardar --}}
            <div class="flex justify-end mt-6">
                <x-wire-button type="submit" blue>
                    Guardar
                </x-wire-button>
            </div>

        </form>
    </x-wire-card>

</x-admin-layout>