<x-admin-layout 
    title="Doctores | MediMatch"
    :breadcrumbs="[
        [
            'name' => 'Dashboard', 
            'href' => route('admin.dashboard')
        ],
        [
            'name' => 'Doctores', 
            'href' => route('admin.doctors.index')
        ],
        [
            'name' => 'Editar'],
    ]">

    <form action="{{ route('admin.doctors.update', $doctor) }}" method="POST">
        @csrf
        @method('PUT')

        <x-wire-card class="mb-8">
            <div class="lg:flex lg:justify-between lg:items-start">
                <div class="flex items-center">
                    <img src="{{ $doctor->user->profile_photo_url }}" alt="{{ $doctor->user->name }}"
                        class="h-24 w-24 rounded-full object-cover object-center shadow-sm">
                    <div class="ml-6">
                        <p class="text-2xl font-bold text-gray-900">Dr(a). {{ $doctor->user->name }}</p>
                        <p class="text-sm text-gray-500 mb-2">{{ $doctor->user->email }}</p>

                        {{-- AQUÍ ESTÁ EL REQUERIMIENTO DEL N/A --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-4 bg-gray-50 p-4 rounded-lg border border-gray-100">
                            <div>
                                <span class="text-xs font-semibold text-gray-500 uppercase">Cédula Profesional:</span>
                                <p class="text-gray-800 font-medium">
                                    {{-- Si tiene cédula la muestra, si no, muestra N/A --}}
                                    {{ $doctor->professional_license ? $doctor->professional_license : 'N/A' }}
                                </p>
                            </div>
                            <div>
                                <span class="text-xs font-semibold text-gray-500 uppercase">Especialidad:</span>
                                <p class="text-gray-800 font-medium">
                                    {{ $doctor->speciality ? $doctor->speciality->name : 'No asignada' }}
                                </p>
                            </div>
                            <div class="sm:col-span-2">
                                <span class="text-xs font-semibold text-gray-500 uppercase">Biografía:</span>
                                <p class="text-gray-800 text-sm mt-1">
                                    {{-- Si tiene biografía la muestra, si no, muestra N/A --}}
                                    {{ $doctor->biography ? $doctor->biography : 'N/A' }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex space-x-3 mt-6 lg:mt-0">
                    <x-wire-button outline gray href="{{ route('admin.doctors.index') }}">Volver</x-wire-button>
                    <x-wire-button type="submit" primary><i class="fa-solid fa-save mr-2"></i> Guardar cambios</x-wire-button>
                </div>
            </div>
        </x-wire-card>

        {{-- TARJETA INFERIOR (FORMULARIO LIMPIO, SIN PESTAÑAS) --}}
        <x-wire-card title="Información Médica">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                {{-- Menú Desplegable de Especialidades --}}
                <div class="md:col-span-2">
                    <x-wire-native-select label="Especialidad Médica" name="speciality_id">
                        <option value="">Seleccione una especialidad</option>
                        @foreach ($specialties as $speciality)
                            <option value="{{ $speciality->id }}" @selected(old('speciality_id', $doctor->speciality_id) == $speciality->id)>
                                {{ $speciality->name }}
                            </option>
                        @endforeach
                    </x-wire-native-select>
                </div>

                {{-- Campo Cédula --}}
                <div class="md:col-span-2">
                    <x-wire-input 
                        label="Cédula Profesional" 
                        name="professional_license" 
                        placeholder="Ej. 12345678"
                        value="{{ old('professional_license', $doctor->professional_license) }}" 
                    />
                </div>

                {{-- Campo Biografía --}}
                <div class="md:col-span-2">
                    <x-wire-textarea 
                        label="Biografía / Perfil Profesional" 
                        name="biography" 
                        placeholder="Escriba un breve resumen de la experiencia del doctor..."
                        rows="4">
                        {{ old('biography', $doctor->biography) }}
                    </x-wire-textarea>
                </div>

            </div>
        </x-wire-card>
    </form>
</x-admin-layout>