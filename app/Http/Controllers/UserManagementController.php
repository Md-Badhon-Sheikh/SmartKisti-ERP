<?php

namespace App\Http\Controllers;

use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    /**
     * Roles that can be assigned from the create/edit user forms.
     * "super-admin" is intentionally excluded — it can only be granted
     * via the dedicated "Make Super Admin" action, by an existing super admin.
     */
    protected function assignableRoles()
    {
        return Role::where('name', '!=', 'super-admin')->pluck('name');
    }

    public function index(): View
    {
        $users = User::with('roles')->latest()->paginate(15);

        return view('users.index', ['users' => $users]);
    }

    public function create(): View
    {
        return view('users.create', ['roles' => $this->assignableRoles()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', 'unique:users,mobile'],
            'email' => ['nullable', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', 'min:8'],
            'role' => ['required', Rule::in(RolePermissionSeeder::ROLES)],
        ]);

        if ($validated['role'] === 'super-admin' && ! $request->user()->hasRole('super-admin')) {
            abort(403);
        }

        $user = User::create([
            'name' => $validated['name'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'] ?? null,
            'password' => $validated['password'],
        ]);

        $user->assignRole($validated['role']);

        return redirect()->route('users.index')->with('status', __('User created successfully.'));
    }

    public function show(User $user): View
    {
        return view('users.show', ['user' => $user]);
    }

    public function edit(Request $request, User $user): View
    {
        if ($user->hasRole('super-admin') && ! $request->user()->hasRole('super-admin')) {
            abort(403);
        }

        return view('users.edit', ['user' => $user, 'roles' => $this->assignableRoles()]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        if ($user->hasRole('super-admin') && ! $request->user()->hasRole('super-admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'mobile' => ['required', 'string', Rule::unique('users', 'mobile')->ignore($user->id)],
            'email' => ['nullable', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', 'min:8'],
            'role' => ['required', Rule::in(RolePermissionSeeder::ROLES)],
        ]);

        if ($validated['role'] === 'super-admin' && ! $request->user()->hasRole('super-admin')) {
            abort(403);
        }

        $user->fill([
            'name' => $validated['name'],
            'mobile' => $validated['mobile'],
            'email' => $validated['email'] ?? null,
        ]);

        if (! empty($validated['password'])) {
            $user->password = $validated['password'];
        }

        $user->save();
        $user->syncRoles([$validated['role']]);

        return redirect()->route('users.index')->with('status', __('User updated successfully.'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->id === $request->user()->id) {
            return back()->with('error', __('You cannot delete yourself.'));
        }

        if ($user->hasRole('super-admin') && User::role('super-admin')->count() <= 1) {
            return back()->with('error', __('The last Super Admin cannot be deleted.'));
        }

        $user->delete();

        return redirect()->route('users.index')->with('status', __('User deleted.'));
    }

    public function promoteSuperAdmin(User $user): RedirectResponse
    {
        $user->syncRoles(['super-admin']);

        return back()->with('status', __(':name has been made a Super Admin.', ['name' => $user->name]));
    }
}
