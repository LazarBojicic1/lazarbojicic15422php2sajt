<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    private const PROTECTED_ROLES = ['admin', 'moderator', 'user'];

    public function index()
    {
        $roles = Role::query()
            ->withCount('users')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        return view('admin.roles.create', [
            'role' => new Role(),
        ]);
    }

    public function store(Request $request)
    {
        $role = Role::create($this->validatedData($request));

        return redirect()
            ->route('admin.roles.edit', $role)
            ->with('message', 'Role created successfully.');
    }

    public function edit(Role $role)
    {
        $role->loadCount('users');

        return view('admin.roles.edit', compact('role'));
    }

    public function update(Request $request, Role $role)
    {
        if (in_array($role->name, self::PROTECTED_ROLES, true) && $request->input('name') !== $role->name) {
            return back()->withErrors([
                'name' => 'Core roles cannot be renamed because they are used by authorization rules.',
            ])->withInput();
        }

        $role->update($this->validatedData($request, $role));

        return redirect()
            ->route('admin.roles.edit', $role)
            ->with('message', 'Role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if (in_array($role->name, self::PROTECTED_ROLES, true)) {
            return back()->withErrors([
                'delete' => 'Core roles cannot be deleted.',
            ]);
        }

        if ($role->users()->exists()) {
            return back()->withErrors([
                'delete' => 'Reassign users before deleting this role.',
            ]);
        }

        $role->delete();

        return redirect()
            ->route('admin.roles.index')
            ->with('message', 'Role deleted successfully.');
    }

    private function validatedData(Request $request, ?Role $role = null): array
    {
        return $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                'alpha_dash',
                Rule::unique('roles', 'name')->ignore($role?->id),
            ],
            'description' => ['nullable', 'string', 'max:500'],
        ]);
    }
}
