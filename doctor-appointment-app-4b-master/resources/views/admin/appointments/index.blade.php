<x-admin-layout 
    title="Citas Médicas | MediMatch"
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],
        [
            'name' => 'Citas',
        ],
    ]">

    {{-- Slot de acción: botón para crear nueva cita --}}
    <x-slot name="action">
        <a href="{{ route('admin.appointments.create') }}"
           class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 inline-flex items-center">
            <i class="fa-solid fa-plus mr-2"></i>
            Nuevo
        </a>
    </x-slot>

    @livewire('admin.datatables.appointment-table')

</x-admin-layout>
