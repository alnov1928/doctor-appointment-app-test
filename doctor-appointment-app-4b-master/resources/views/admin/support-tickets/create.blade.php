<x-admin-layout 
    title="Nuevo Ticket | MediMatch"
    :breadcrumbs="[
        [
            'name' => 'Dashboard',
            'href' => route('admin.dashboard'),
        ],
        [
            'name' => 'Soporte',
            'href' => route('admin.support-tickets.index'),
        ],
        [
            'name' => 'Nuevo Ticket',
        ],
    ]">

    {{-- Formulario para crear un nuevo ticket de soporte --}}
    <div class="max-w-3xl mx-auto mt-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-2">Reportar un problema</h2>
            <p class="text-gray-500 mb-6">Describe tu problema o duda y nuestro equipo de soporte se pondrá en contacto contigo.</p>

            <form action="{{ route('admin.support-tickets.store') }}" method="POST">
                @csrf

                {{-- Campo: Título del problema --}}
                <div class="mb-5">
                    <label for="title" class="block mb-2 text-sm font-medium text-gray-900">
                        Título del problema
                    </label>
                    <input type="text" 
                           id="title" 
                           name="title" 
                           value="{{ old('title') }}"
                           class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('title') border-red-500 @enderror"
                           placeholder="Ej: No puedo agendar una cita"
                           required>
                    @error('title')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Campo: Descripción detallada --}}
                <div class="mb-5">
                    <label for="description" class="block mb-2 text-sm font-medium text-gray-900">
                        Descripción detallada
                    </label>
                    <textarea id="description" 
                              name="description" 
                              rows="5"
                              class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5 @error('description') border-red-500 @enderror"
                              placeholder="Describe tu problema con el mayor detalle posible..."
                              required>{{ old('description') }}</textarea>
                    @error('description')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Botones de acción --}}
                <div class="flex justify-end gap-3">
                    <a href="{{ route('admin.support-tickets.index') }}"
                       class="py-2.5 px-5 text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200 hover:bg-gray-100 hover:text-blue-700 focus:ring-4 focus:ring-gray-100">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5">
                        Enviar Ticket
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-admin-layout>
