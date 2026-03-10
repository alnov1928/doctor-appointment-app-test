<x-admin-layout 
    title="Nueva Cita | MediMatch"
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
            'name' => 'Nuevo',
        ],
    ]">

    <div x-data="appointmentCreator()" class="space-y-6">

        {{-- SECCIÓN 1: Buscar disponibilidad --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-1">Buscar disponibilidad</h2>
            <p class="text-gray-500 mb-5 text-sm">Encuentra el horario perfecto para tu cita.</p>

            <form action="{{ route('admin.appointments.create') }}" method="GET">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    {{-- Campo: Fecha --}}
                    <div>
                        <label for="search_date" class="block mb-2 text-sm font-medium text-gray-900">Fecha</label>
                        <input type="date" 
                               id="search_date" 
                               name="date" 
                               value="{{ request('date', now()->format('Y-m-d')) }}"
                               class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                               required>
                    </div>

                    {{-- Campo: Hora --}}
                    <div>
                        <label for="search_hour" class="block mb-2 text-sm font-medium text-gray-900">Hora</label>
                        <select id="search_hour" 
                                name="hour" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                required>
                            <option value="">Selecciona una hora</option>
                            @for ($h = 8; $h <= 17; $h++)
                                <option value="{{ $h }}" @selected(request('hour') == $h)>
                                    {{ sprintf('%02d:00', $h) }}
                                </option>
                            @endfor
                        </select>
                    </div>

                    {{-- Campo: Especialidad (opcional) --}}
                    <div>
                        <label for="search_speciality" class="block mb-2 text-sm font-medium text-gray-900">Especialidad (opcional)</label>
                        <select id="search_speciality" 
                                name="speciality_id" 
                                class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option value="">Selecciona una espec...</option>
                            @foreach ($specialties as $speciality)
                                <option value="{{ $speciality->id }}" @selected(request('speciality_id') == $speciality->id)>
                                    {{ $speciality->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Botón: Buscar --}}
                    <div>
                        <button type="submit"
                                class="w-full text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5">
                            Buscar disponibilidad
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- SECCIÓN 2: Resultados de disponibilidad --}}
        @if($searched)
            @if($availableDoctors->isNotEmpty())
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Doctores disponibles</h3>
                    <p class="text-gray-500 text-sm mb-5">
                        {{ $availableDoctors->count() }} doctor(es) disponible(s) el 
                        <strong>{{ \Carbon\Carbon::parse(request('date'))->translatedFormat('d/m/Y (l)') }}</strong>
                        a las <strong>{{ sprintf('%02d:00', request('hour')) }}</strong>
                    </p>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach($availableDoctors as $doctor)
                            <div class="border rounded-lg p-4 cursor-pointer transition-all duration-200"
                                 :class="selectedDoctor === {{ $doctor->id }} ? 'border-indigo-500 bg-indigo-50 ring-2 ring-indigo-200' : 'border-gray-200 hover:border-indigo-300 hover:bg-gray-50'"
                                 @click="selectDoctor({{ $doctor->id }})">
                                
                                {{-- Info del doctor --}}
                                <div class="flex items-center mb-3">
                                    <img src="{{ $doctor->user->profile_photo_url }}" 
                                         alt="{{ $doctor->user->name }}"
                                         class="h-12 w-12 rounded-full object-cover">
                                    <div class="ml-3">
                                        <p class="font-semibold text-gray-900">{{ $doctor->user->name }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $doctor->speciality ? $doctor->speciality->name : 'Sin especialidad' }}
                                        </p>
                                    </div>
                                </div>

                                {{-- Slots disponibles --}}
                                <p class="text-xs font-semibold text-gray-500 uppercase mb-2">Horarios disponibles:</p>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($doctor->freeSlots as $slot)
                                        @php
                                            $slotStart = \Carbon\Carbon::parse($slot->start_time)->format('H:i');
                                            $slotEnd = \Carbon\Carbon::parse($slot->end_time)->format('H:i');
                                        @endphp
                                        <button type="button"
                                                class="px-3 py-1 text-xs font-medium rounded-full transition-all duration-200"
                                                :class="selectedDoctor === {{ $doctor->id }} && selectedSlot === '{{ $slotStart }}' 
                                                    ? 'bg-indigo-600 text-white' 
                                                    : 'bg-gray-100 text-gray-700 hover:bg-indigo-100 hover:text-indigo-700'"
                                                @click.stop="selectSlot({{ $doctor->id }}, '{{ $slotStart }}', '{{ $slotEnd }}')">
                                            {{ $slotStart }} - {{ $slotEnd }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- SECCIÓN 3: Formulario de creación (aparece al seleccionar doctor y slot) --}}
                <div x-show="selectedDoctor && selectedSlot" 
                     x-transition
                     class="bg-white rounded-lg shadow-md p-6">
                    
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Confirmar cita</h3>
                    <p class="text-gray-500 text-sm mb-5">Selecciona el paciente y confirma la cita.</p>

                    <form action="{{ route('admin.appointments.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="doctor_id" x-bind:value="selectedDoctor">
                        <input type="hidden" name="date" value="{{ request('date') }}">
                        <input type="hidden" name="start_time" x-bind:value="selectedSlot">
                        <input type="hidden" name="end_time" x-bind:value="selectedSlotEnd">
                        <input type="hidden" name="status" value="Programado">

                        {{-- Resumen de la selección --}}
                        <div class="bg-indigo-50 border border-indigo-200 rounded-lg p-4 mb-5">
                            <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                                <div>
                                    <span class="text-indigo-500 font-semibold block">Fecha</span>
                                    <span class="text-gray-900">{{ \Carbon\Carbon::parse(request('date'))->format('d/m/Y') }}</span>
                                </div>
                                <div>
                                    <span class="text-indigo-500 font-semibold block">Horario</span>
                                    <span class="text-gray-900" x-text="selectedSlot + ' - ' + selectedSlotEnd"></span>
                                </div>
                                <div>
                                    <span class="text-indigo-500 font-semibold block">Doctor</span>
                                    <span class="text-gray-900" x-text="selectedDoctorName"></span>
                                </div>
                                <div>
                                    <span class="text-indigo-500 font-semibold block">Especialidad</span>
                                    <span class="text-gray-900" x-text="selectedDoctorSpeciality"></span>
                                </div>
                            </div>
                        </div>

                        {{-- Campo: Paciente --}}
                        <div class="mb-5">
                            <label for="patient_id" class="block mb-2 text-sm font-medium text-gray-900">
                                Paciente
                            </label>
                            <select id="patient_id" 
                                    name="patient_id" 
                                    class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5"
                                    required>
                                <option value="">Seleccione un paciente</option>
                                @foreach ($patients as $patient)
                                    <option value="{{ $patient->id }}">
                                        {{ $patient->user->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('patient_id')
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
                                    class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5">
                                <i class="fa-solid fa-calendar-check mr-2"></i>
                                Crear Cita
                            </button>
                        </div>
                    </form>
                </div>

            @else
                {{-- Sin resultados --}}
                <div class="bg-white rounded-lg shadow-md p-12 text-center">
                    <i class="fa-solid fa-calendar-xmark text-gray-300 text-5xl mb-4"></i>
                    <h3 class="text-lg font-semibold text-gray-500">No hay disponibilidad</h3>
                    <p class="text-sm text-gray-400 mt-1">
                        No se encontraron doctores disponibles para la fecha y hora seleccionada. 
                        Intente con otra fecha, hora o especialidad.
                    </p>
                </div>
            @endif
        @endif

    </div>

    {{-- Script Alpine.js para la selección de doctor/slot --}}
    <script>
        function appointmentCreator() {
            return {
                selectedDoctor: null,
                selectedDoctorName: '',
                selectedDoctorSpeciality: '',
                selectedSlot: null,
                selectedSlotEnd: null,

                selectDoctor(doctorId) {
                    if (this.selectedDoctor !== doctorId) {
                        this.selectedDoctor = doctorId;
                        this.selectedSlot = null;
                        this.selectedSlotEnd = null;
                    }
                },

                selectSlot(doctorId, startTime, endTime) {
                    this.selectedDoctor = doctorId;
                    this.selectedSlot = startTime;
                    this.selectedSlotEnd = endTime;

                    // Obtener el nombre del doctor del DOM
                    const card = event.target.closest('[x-bind\\:class], [\\:class]');
                    if (card) {
                        const nameEl = card.querySelector('.font-semibold.text-gray-900');
                        const specEl = card.querySelector('.text-xs.text-gray-500');
                        this.selectedDoctorName = nameEl ? nameEl.textContent.trim() : '';
                        this.selectedDoctorSpeciality = specEl ? specEl.textContent.trim() : '';
                    }
                }
            };
        }
    </script>

</x-admin-layout>
