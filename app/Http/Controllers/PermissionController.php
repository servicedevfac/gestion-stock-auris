<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $permissions = Permission::paginate(15);
        return view('admin.permissions.index', compact('permissions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.permissions.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name',
        ]);
        Permission::create(['name' => $request->name, 'guard_name' => 'web']);
        return redirect()->route('permissions.index')->with('success', 'Permission a été créée avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $permission = Permission::findOrFail($id);
        return view('admin.permissions.edit', compact('permission'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|unique:permissions,name,' . $id,
        ]);
        $permission = Permission::findOrFail($id);
        $permission->update(['name' => $request->name]);

        return redirect()->route('permissions.index')->with('success', 'Permission a été mise à jour avec succès');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( $id)
    {
        Permission::findOrFail($id)->delete();
        return redirect()->route('permissions.index')->with('success', 'Permission  a été supprimée');
    }
}
