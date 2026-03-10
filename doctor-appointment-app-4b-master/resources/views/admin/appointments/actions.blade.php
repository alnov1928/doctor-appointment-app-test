<div class="flex items-center space-x-2">
    {{-- Botón de editar --}}
    <x-wire-button href="{{ route('admin.appointments.edit', $appointment) }}" blue xs>
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>

    {{-- Botón de eliminar --}}
    <form action="{{ route('admin.appointments.destroy', $appointment) }}" method="POST" class="delete-form inline">
        @csrf
        @method('DELETE')
        <x-wire-button type="submit" green xs onclick="return confirm('¿Estás seguro de eliminar esta cita?')">
            <i class="fa-solid fa-trash"></i>
        </x-wire-button>
    </form>
</div>
