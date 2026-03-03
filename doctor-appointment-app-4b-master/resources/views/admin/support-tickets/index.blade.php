<x-admin-layout 
    title="Soporte | MediMatch"
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],
        [
            'name' => 'Soporte',
        ],
    ]">

    {{-- Slot de acción: botón para crear nuevo ticket --}}
    <x-slot name="action">
        <a href="{{ route('admin.support-tickets.create') }}"
           class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
            Nuevo Ticket
        </a>
    </x-slot>

    {{-- Tabla de tickets de soporte --}}
    <div class="relative overflow-x-auto shadow-md sm:rounded-lg mt-4">
        <table class="w-full text-sm text-left text-gray-500">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                <tr>
                    <th scope="col" class="px-6 py-3">ID</th>
                    <th scope="col" class="px-6 py-3">Usuario</th>
                    <th scope="col" class="px-6 py-3">Título</th>
                    <th scope="col" class="px-6 py-3">Estado</th>
                    <th scope="col" class="px-6 py-3">Fecha</th>
                    <th scope="col" class="px-6 py-3">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr class="bg-white border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-medium text-gray-900">
                            #{{ $ticket->id }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $ticket->user->name }}
                        </td>
                        <td class="px-6 py-4">
                            {{ $ticket->title }}
                        </td>
                        <td class="px-6 py-4">
                            @if($ticket->status === 'abierto')
                                <span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                    Abierto
                                </span>
                            @elseif($ticket->status === 'en_progreso')
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                    En Progreso
                                </span>
                            @elseif($ticket->status === 'cerrado')
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                    Cerrado
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            {{ $ticket->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4">
                            <form action="{{ route('admin.support-tickets.destroy', $ticket) }}" method="POST" class="delete-form inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr class="bg-white border-b">
                        <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                            No hay tickets de soporte registrados.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</x-admin-layout>
