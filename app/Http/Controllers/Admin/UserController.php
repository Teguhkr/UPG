<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Eselonering;
use App\Models\ListSatker;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim($request->input('search', ''));
        $usersQuery = User::with('satker.eselon');

        if ($search !== '') {
            $usersQuery->where(function ($query) use ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%")
                    ->orWhereHas('satker', function ($satkerQuery) use ($search) {
                        $satkerQuery->where('list_satker', 'like', "%{$search}%")
                            ->orWhereHas('eselon', function ($eselonQuery) use ($search) {
                                $eselonQuery->where('nama_eselon', 'like', "%{$search}%")
                                    ->orWhere('eselonering', 'like', "%{$search}%");
                            });
                    });
            });
        }

        $users = $usersQuery->orderBy('id')->paginate(15)->withQueryString();
        $eseloneringRows = Eselonering::orderBy('no_urut')->get();
        $satkers = ListSatker::orderBy('list_satker')->get(['id', 'list_satker', 'eselonering']);

        return view('admin.users.index', compact('users', 'eseloneringRows', 'satkers', 'search'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:80', 'unique:users,username'],
            'email' => ['required', 'email', 'max:150', 'unique:users,email'],
            'role' => ['required', 'in:super_admin,editor,operator'],
            'eselonering_id' => ['required', 'integer', Rule::exists('eselonering', 'id')],
            'satker_id' => [
                'required',
                'integer',
                Rule::exists('list_satker', 'id')->where('eselonering', $request->input('eselonering_id')),
            ],
            'password' => ['required', 'string', 'min:6'],
        ]);

        User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'role' => $data['role'],
            'satker_id' => $data['satker_id'],
            'password' => Hash::make($data['password']),
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna baru berhasil ditambahkan.');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'username' => ['required', 'string', 'max:80', Rule::unique('users', 'username')->ignore($user->id)],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
            'role' => ['required', 'in:super_admin,editor,operator'],
            'eselonering_id' => ['required', 'integer', Rule::exists('eselonering', 'id')],
            'satker_id' => [
                'required',
                'integer',
                Rule::exists('list_satker', 'id')->where('eselonering', $request->input('eselonering_id')),
            ],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $payload = [
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'role' => $data['role'],
            'satker_id' => $data['satker_id'],
            'is_active' => $request->boolean('is_active'),
        ];

        if (! empty($data['password'])) {
            $payload['password'] = Hash::make($data['password']);
        }

        $user->update($payload);

        return redirect()->route('admin.users.index')
            ->with('success', 'Data pengguna berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return redirect()->route('admin.users.index')
                ->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'Pengguna berhasil dihapus.');
    }
}

