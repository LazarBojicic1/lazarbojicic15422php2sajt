<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::query()
            ->with('role')
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = trim((string) $request->input('q'));

                $query->where(function ($builder) use ($search) {
                    $builder
                        ->where('name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%');
                });
            })
            ->when($request->filled('role'), fn ($query) => $query->where('role_id', $request->integer('role')))
            ->when($request->filled('status'), function ($query) use ($request) {
                if ($request->input('status') === 'active') {
                    $query->where('is_active', true);
                }

                if ($request->input('status') === 'inactive') {
                    $query->where('is_active', false);
                }
            })
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $roles = Role::query()->orderBy('name')->get();

        return view('admin.users.index', compact('users', 'roles'));
    }

    public function create()
    {
        $roles = Role::query()->orderBy('name')->get();

        return view('admin.users.create', [
            'roles' => $roles,
            'user' => new User(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $user = User::create($data);

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('message', 'User created successfully.');
    }

    public function edit(User $user)
    {
        $roles = Role::query()->orderBy('name')->get();
        $user->load('role');

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $this->validatedData($request, $user);

        if (blank($data['password'] ?? null)) {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()
            ->route('admin.users.edit', $user)
            ->with('message', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->is(auth()->user())) {
            return back()->withErrors([
                'delete' => 'You cannot delete the account you are currently using.',
            ]);
        }

        if ($user->isAdmin() && User::query()->whereHas('role', fn ($query) => $query->where('name', 'admin'))->count() <= 1) {
            return back()->withErrors([
                'delete' => 'At least one administrator account must remain active.',
            ]);
        }

        $user->delete();

        return redirect()
            ->route('admin.users.index')
            ->with('message', 'User deleted successfully.');
    }

    private function validatedData(Request $request, ?User $user = null): array
    {
        $validated = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'name')->ignore($user?->id),
            ],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user?->id),
            ],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:8', 'confirmed'],
        ]);

        $validated['is_active'] = $request->boolean('is_active');
        $validated['email_verified_at'] = $request->boolean('email_verified')
            ? ($user?->email_verified_at ?? now())
            : null;

        return $validated;
    }
}
