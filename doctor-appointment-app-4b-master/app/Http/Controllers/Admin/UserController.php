<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        
        return view('admin.users.index'); 
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'id_number' => 'required|string|min:5|max:20|regex:/^[A-Za-z0-9\-]+$/|unique:users',
            'phone' => 'required|digits_between:7,15',
            'address' => 'required|string|min:3|max:255',
            'role_id' => 'required|exists:roles,id',
        ]);

      
        $data['password'] = bcrypt($data['password']);

  
        $user = User::create($data);

        $user->roles()->sync([$data['role_id']]);

      
        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Usuario creado correctamente',
            'text' => 'El usuario se ha registrado en el sistema.',
        ]);

        // Si es Doctor
        if ($user->hasRole('Doctor')) { 
            $doctor = \App\Models\Doctor::create([
                'user_id' => $user->id
            ]);
            return redirect()->route('admin.doctors.edit', $doctor)
                ->with('success', 'Complete la información médica del doctor.');
        }

        // Si es Paciente
        if ($user->hasRole('Paciente')) { 
            $patient = \App\Models\Patient::create([
                'user_id' => $user->id
            ]);
            return redirect()->route('admin.patients.edit', $patient)
                ->with('success', 'Complete los antecedentes del paciente.');
        }

        // Si es un Administrador, Recepcionista, etc. (No necesita perfil extra)
        return redirect()->route('admin.users.index')
            ->with('success', 'Usuario administrativo creado exitosamente.');
    }



    /**
     * Display the specified resource.
     */
    public function show(User $user) 
    {
        // 
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user) 
    {
        $roles = Role::all();
        return view('admin.users.edit', compact('user', 'roles'));
        
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user) 
    {
        $data = $request->validate([
            'name' => 'required|string|min:3|max:255',
            'email' => 'required|string|email|unique:users,email,' . $user->id,
            'password' => 'required|string|min:8|confirmed',
            'id_number' => 'required|string|min:5|max:20|regex:/^[A-Za-z0-9\-]+$/|unique:users,id_number,' . $user->id,
            'phone' => 'required|digits_between:7,15',
            'address' => 'required|string|min:3|max:255',
            'role_id' => 'required|exists:roles,id',
        ]);
        $user->update($data);
        $user->roles()->attach($data['role_id']);

        session () ->flash('swal', [
            'icon' => 'success',
            'title' => 'Usuario creado correctamente',
            'text' => 'El usuario se ha creado correctamente.',
        ]);

        return redirect()->route('admin.users.edit', $user->id)->with ('success', 'Usuario actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user) 
    {
        if (Auth::id() == $user->id) {
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Error al eliminar usuario',
                'text' => 'No puedes eliminar tu propia cuenta.',
            ]);
            abort(403, 'No puedes eliminar tu propia cuenta.');
        }
        
        //Eliminar roles realacionados a usuario
        $user->roles()->detach();

        $user->delete();

        session () ->flash('swal', [
            'icon' => 'success',
            'title' => 'Usuario eliminado correctamente',
            'text' => 'El usuario se ha eliminado correctamente.',
        ]);

        return redirect()->route('admin.users.index')->with ('success', 'Usuario eliminado exitosamente.');
    }
}