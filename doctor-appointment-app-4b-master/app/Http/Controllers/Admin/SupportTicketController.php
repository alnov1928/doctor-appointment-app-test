<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use Illuminate\Http\Request;

class SupportTicketController extends Controller
{
    /**
     * Mostrar la lista de tickets de soporte.
     */
    public function index()
    {
        // Obtener todos los tickets con la relación del usuario, ordenados por más recientes
        $tickets = SupportTicket::with('user')->latest()->get();
        return view('admin.support-tickets.index', compact('tickets'));
    }

    /**
     * Mostrar el formulario para crear un nuevo ticket.
     */
    public function create()
    {
        return view('admin.support-tickets.create');
    }

    /**
     * Guardar un nuevo ticket en la base de datos.
     */
    public function store(Request $request)
    {
        // Validar los datos del formulario
        $data = $request->validate([
            'title' => 'required|string|min:3|max:255',
            'description' => 'required|string|min:10|max:2000',
        ]);

        // Crear el ticket asociado al usuario autenticado
        $request->user()->supportTickets()->create($data);

        // Mostrar mensaje de éxito con SweetAlert
        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Ticket enviado!',
            'text' => 'Tu ticket de soporte ha sido registrado exitosamente.',
        ]);

        return redirect()->route('admin.support-tickets.index');
    }

    /**
     * Eliminar un ticket de soporte.
     */
    public function destroy(SupportTicket $supportTicket)
    {
        $supportTicket->delete();

        // Mostrar mensaje de éxito con SweetAlert
        session()->flash('swal', [
            'icon' => 'success',
            'title' => '¡Ticket eliminado!',
            'text' => 'El ticket de soporte ha sido eliminado exitosamente.',
        ]);

        return redirect()->route('admin.support-tickets.index');
    }
}
