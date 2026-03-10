@php
    // Definir los días de la semana
    $days = [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
        7 => 'Domingo',
    ];

    // Generar los bloques de horas (8:00 a 17:00) con intervalos de 15 minutos
    $hourBlocks = [];
    for ($h = 8; $h <= 17; $h++) {
        $slots = [];
        for ($m = 0; $m < 60; $m += 15) {
            $start = sprintf('%02d:%02d', $h, $m);
            $endMinutes = $h * 60 + $m + 15;
            $end = sprintf('%02d:%02d', intdiv($endMinutes, 60), $endMinutes % 60);
            $slots[] = ['start' => $start, 'end' => $end];
        }
        $hourBlocks[sprintf('%02d:00:00', $h)] = $slots;
    }
@endphp

<x-admin-layout 
    title="Horarios | MediMatch"
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],
        [
            'name' => 'Horarios',
        ],
    ]">

    <div x-data="scheduleManager()" class="space-y-6">

        {{-- Selector de doctor --}}
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                <div class="flex-1">
                    <label for="doctor_selector" class="block mb-2 text-sm font-medium text-gray-900">
                        Seleccionar Doctor
                    </label>
                    <select id="doctor_selector"
                            onchange="window.location.href='{{ route('admin.schedules.index') }}?doctor_id=' + this.value"
                            class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                        <option value="">Seleccione un doctor</option>
                        @foreach ($doctors as $doctor)
                            <option value="{{ $doctor->id }}" @selected($selectedDoctor && $selectedDoctor->id == $doctor->id)>
                                {{ $doctor->user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Grilla de horarios (solo se muestra si hay doctor seleccionado) --}}
        @if($selectedDoctor)
        <form action="{{ route('admin.schedules.store') }}" method="POST">
            @csrf
            <input type="hidden" name="doctor_id" value="{{ $selectedDoctor->id }}">

            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="flex justify-between items-center mb-6">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900">Gestor de horarios</h2>
                        <p class="text-sm text-gray-500 mt-1">Dr(a). {{ $selectedDoctor->user->name }}</p>
                    </div>
                    <button type="submit"
                            class="text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-300 font-medium rounded-lg text-sm px-5 py-2.5">
                        Guardar horario
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 border-b">
                            <tr>
                                <th class="px-4 py-3 min-w-[120px]">Día/Hora</th>
                                @foreach($days as $dayNum => $dayName)
                                    <th class="px-4 py-3 text-center min-w-[130px]">{{ $dayName }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hourBlocks as $hourLabel => $slots)
                                {{-- Fila del grupo de hora con checkboxes "Todos" --}}
                                <tr class="bg-gray-50 border-b border-t">
                                    <td class="px-4 py-3 font-semibold text-gray-900">
                                        <label class="flex items-center cursor-pointer">
                                            <input type="checkbox" 
                                                   class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 mr-2"
                                                   x-on:change="toggleHourAllDays('{{ $hourLabel }}', $event.target.checked)">
                                            {{ $hourLabel }}
                                        </label>
                                    </td>
                                    @foreach($days as $dayNum => $dayName)
                                        <td class="px-4 py-3 text-center">
                                            <label class="flex items-center justify-center cursor-pointer text-xs text-gray-500">
                                                <input type="checkbox" 
                                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 mr-1"
                                                       data-hour-group="{{ $hourLabel }}"
                                                       data-day="{{ $dayNum }}"
                                                       x-on:change="toggleGroup('{{ $hourLabel }}', {{ $dayNum }}, $event.target.checked)">
                                                Todos
                                            </label>
                                        </td>
                                    @endforeach
                                </tr>

                                {{-- Filas de los slots de 15 minutos --}}
                                @foreach($slots as $slot)
                                    <tr class="bg-white border-b hover:bg-gray-50">
                                        <td class="px-4 py-2 text-gray-600 pl-8">
                                            {{ $slot['start'] }} - {{ $slot['end'] }}
                                        </td>
                                        @foreach($days as $dayNum => $dayName)
                                            @php
                                                $slotKey = $dayNum . '_' . $slot['start'];
                                                $isChecked = in_array($slotKey . ':00', $existingSlots) || in_array($slotKey, $existingSlots);
                                            @endphp
                                            <td class="px-4 py-2 text-center">
                                                <label class="flex items-center justify-center cursor-pointer text-xs text-gray-500">
                                                    <input type="checkbox" 
                                                           name="slots[]" 
                                                           value="{{ $slotKey }}"
                                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 mr-1"
                                                           data-hour-group="{{ $hourLabel }}"
                                                           data-day="{{ $dayNum }}"
                                                           data-slot="true"
                                                           {{ $isChecked ? 'checked' : '' }}>
                                                    {{ $slot['start'] }} - {{ $slot['end'] }}
                                                </label>
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </form>
        @else
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <i class="fa-solid fa-calendar-days text-gray-300 text-5xl mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-500">Seleccione un doctor</h3>
                <p class="text-sm text-gray-400 mt-1">Elija un doctor del menú desplegable para gestionar su disponibilidad de horario.</p>
            </div>
        @endif
    </div>

    {{-- Script Alpine.js para la lógica de "Todos" --}}
    <script>
        function scheduleManager() {
            return {
                // Seleccionar/deseleccionar todos los slots de una hora para un día específico
                toggleGroup(hourLabel, day, checked) {
                    const checkboxes = document.querySelectorAll(
                        `input[data-slot="true"][data-hour-group="${hourLabel}"][data-day="${day}"]`
                    );
                    checkboxes.forEach(cb => cb.checked = checked);
                },

                // Seleccionar/deseleccionar todos los slots de una hora para TODOS los días
                toggleHourAllDays(hourLabel, checked) {
                    // Seleccionar todos los checkboxes "Todos" del grupo
                    const groupToggles = document.querySelectorAll(
                        `input[data-hour-group="${hourLabel}"][data-day]:not([data-slot])`
                    );
                    groupToggles.forEach(cb => cb.checked = checked);

                    // Seleccionar todos los slots del grupo
                    const slotCheckboxes = document.querySelectorAll(
                        `input[data-slot="true"][data-hour-group="${hourLabel}"]`
                    );
                    slotCheckboxes.forEach(cb => cb.checked = checked);
                }
            };
        }
    </script>

</x-admin-layout>
