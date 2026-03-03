<div class="flex items-center space-x-2">
    <x-wire-button href="{{ route('admin.doctors.edit', $doctor ?? $row) }}" blue xs>
        <i class="fa-solid fa-pen-to-square"></i>
    </x-wire-button>
</div>