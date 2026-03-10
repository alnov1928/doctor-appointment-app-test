<x-admin-layout 
    title="Editar Cita | MediMatch"
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],
        [
            'name' => 'Citas',
            'href' => route('admin.appointments.index'),
        ],
        [
            'name' => 'Editar',
        ],
    ]">

    {{-- Formulario para editar una cita médica --}}
    <div class="max-w-3xl mx-auto mt-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-2">Editar cita médica</h2>
            <p class="text-gray-500 mb-6">Modifica los datos de la cita médica.</p>

            <form action="{{ route('admin.appointments.update', $appointment) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- Campo: Paciente --}}
                <div class="mb-5">
                    <label for="patient_id" class="block mb-2 text-sm font-medium text-gray-900">
                        Paciente
                    </label>
                    <select id="patient_id" 
                            name="patient_id" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('patient_id') border-red-500 @enderror"
                            required>
                        <option value="">Seleccione un paciente</option>
                        @foreach ($patients as $patient)
                            <option value="{{ $patient->id }}" @selected(old('patient_id', $appointment->patient_id) == $patient->id)>
                                {{ $patient->user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('patient_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Campo: Doctor --}}
                <div class="mb-5">
                    <label for="doctor_id" class="block mb-2 text-sm font-medium text-gray-900">
                        Doctor
                    </label>
                    <select id="doctor_id" 
                            name="doctor_id" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('doctor_id') border-red-500 @enderror"
                            required>
                        <option value="">Seleccione un doctor</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}" @selected(old('doctor_id', $appointment->doctor_id) == $doctor->id)>
                                {{ $doctor->user->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('doctor_id')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Campo: Fecha --}}
                <div class="mb-5">
                    <label for="date" class="block mb-2 text-sm font-medium text-gray-900">
                        Fecha
                    </label>
                    <input type="date" 
                           id="date" 
                           name="date" 
                           value="{{ old('date', $appointment->date) }}"
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('date') border-red-500 @enderror"
                           required>
                    @error('date')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Campos: Hora inicio y Hora fin --}}
                <div class="grid grid-cols-2 gap-4 mb-5">
                    <div>
                        <label for="start_time" class="block mb-2 text-sm font-medium text-gray-900">
                            Hora de inicio
                        </label>
                        <input type="time" 
                               id="start_time" 
                               name="start_time" 
                               value="{{ old('start_time', \Carbon\Carbon::parse($appointment->start_time)->format('H:i')) }}"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('start_time') border-red-500 @enderror"
                               required>
                        @error('start_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label for="end_time" class="block mb-2 text-sm font-medium text-gray-900">
                            Hora de fin
                        </label>
                        <input type="time" 
                               id="end_time" 
                               name="end_time" 
                               value="{{ old('end_time', \Carbon\Carbon::parse($appointment->end_time)->format('H:i')) }}"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('end_time') border-red-500 @enderror"
                               required>
                        @error('end_time')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Campo: Estado --}}
                <div class="mb-5">
                    <label for="status" class="block mb-2 text-sm font-medium text-gray-900">
                        Estado
                    </label>
                    <select id="status" 
                            name="status" 
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('status') border-red-500 @enderror"
                            required>
                        <option value="Programado" @selected(old('status', $appointment->status) == 'Programado')>Programado</option>
                        <option value="Completado" @selected(old('status', $appointment->status) == 'Completado')>Completado</option>
                        <option value="Cancelado" @selected(old('status', $appointment->status) == 'Cancelado')>Cancelado</option>
                    </select>
                    @error('status')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Botones de acción --}}
                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.appointments.index') }}"
                       class="py-2.5 px-5 text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:ring-4 focus:ring-gray-100">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                        Actualizar Cita
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-admin-layout>
