<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('admin.roles.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.roles.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //Validación
        $request->validate(['name' => 'required|unique:roles,name',]); 
        
        //Si pasa se creara el rol
        Role::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        //Variable de un solo uso
        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Rol creado correctamente',
            'text' => 'El rol se ha creado correctamente.',
        ]);

        //Redireccionar a la tabla
        return redirect()->route('admin.roles.index')->with('success', 'Rol creado exitosamente.');

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Role $role)
    {
        return view('admin.roles.edit' , compact('role'));
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        //Validación
        $request->validate(['name' => 'required|unique:roles,name,' . $role->id]); 
        

        //Si el camo no cambio no actualices 
        if ($role->name === $request->name) {
                    session()->flash('swal', 
            [
                'icon' => 'info',
                'title' => 'No se realizaron cambios',
                'text' => 'No se detectaron modificaciones.',
            ]);
            //Redureccion al mismo lugar
            return redirect()->route('admin.roles.edit', $role);
        }

        //Si pasa se actualiza el rol
        $role->update([ 'name' => $request->name]);

        //Variable de un solo usox
        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Rol actualizado correctamente',
            'text' => 'El rol se ha actualizado correctamente.',
        ]);

        //Redireccionar a la tabla
        return redirect()->route('admin.roles.index', $role);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        //Restringir la accion
        if ($role->id <= 4) {
            //Variable de uso
            session()->flash('swal', [
                'icon' => 'error',
                'title' => 'Acción no permitida',
                'text' => 'No se puede eliminar este rol.',
            ]);
            return redirect()->route('admin.roles.index');
        }

        //Borrar el rol
        $role->delete();

        //Alerta
        session()->flash('swal', [
            'icon' => 'success',
            'title' => 'Rol eliminado correctamente',
            'text' => 'El rol se ha eliminado correctamente.',
        ]);

        //Redireccionar a la tabla
        return redirect()->route('admin.roles.index');
    }
}
