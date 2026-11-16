@extends('layouts/layoutMaster')

@section('title', 'Pengguna Admin')

@section('content')
@if(session('success'))
  <div class="alert alert-success alert-dismissible">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
  <div class="alert alert-danger alert-dismissible">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="row g-6">
  <div class="col-lg-4">
    <div class="card">
      <div class="card-header"><h5 class="mb-0">Tambah Pengguna Admin</h5></div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.users.store') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label">Nama Lengkap</label>
            <input type="text" name="name" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required />
          </div>
          <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
              <option value="super_admin">Super Admin</option>
              <option value="editor">Editor</option>
              <option value="operator">Operator</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" minlength="6" required />
          </div>
          <div class="form-check mb-4">
            <input type="checkbox" class="form-check-input" id="u_active" name="is_active" value="1" checked />
            <label class="form-check-label" for="u_active">Akun Aktif</label>
          </div>
          <button type="submit" class="btn btn-primary w-100">Tambah Pengguna</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card">
      <div class="card-header"><h5 class="mb-0">Daftar Pengguna Admin</h5></div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Nama / Username</th>
              <th>Role</th>
              <th>Status</th>
              <th>Login Terakhir</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($users as $user)
              <tr>
                <td>
                  <div class="fw-semibold">{{ $user->name }}</div>
                  <small class="text-body-secondary">{{ $user->username }} • {{ $user->email }}</small>
                </td>
                <td><span class="badge bg-label-primary">{{ strtoupper(str_replace('_', ' ', $user->role)) }}</span></td>
                <td><span class="badge bg-label-{{ $user->is_active ? 'success' : 'secondary' }}">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                <td><small>{{ optional($user->last_login_at)->format('d/m/Y H:i') ?: '-' }}</small></td>
                <td class="text-end text-nowrap">
                  <button type="button" class="btn btn-sm btn-icon btn-label-primary" data-bs-toggle="modal" data-bs-target="#editUserModal{{ $user->id }}">
                    <i class="icon-base ti tabler-edit"></i>
                  </button>
                  @if($user->id !== auth()->id())
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pengguna ini?')">
                      @csrf @method('DELETE')
                      <button type="submit" class="btn btn-sm btn-icon btn-label-danger"><i class="icon-base ti tabler-trash"></i></button>
                    </form>
                  @endif
                </td>
              </tr>

              <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                  <form method="POST" action="{{ route('admin.users.update', $user) }}" class="modal-content">
                    @csrf @method('PUT')
                    <div class="modal-header">
                      <h5 class="modal-title">Edit Pengguna</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-3"><label class="form-label">Nama</label><input type="text" name="name" value="{{ $user->name }}" class="form-control" required /></div>
                      <div class="mb-3"><label class="form-label">Username</label><input type="text" name="username" value="{{ $user->username }}" class="form-control" required /></div>
                      <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" value="{{ $user->email }}" class="form-control" required /></div>
                      <div class="mb-3">
                        <label class="form-label">Role</label>
                        <select name="role" class="form-select">
                          <option value="super_admin" {{ $user->role === 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                          <option value="editor" {{ $user->role === 'editor' ? 'selected' : '' }}>Editor</option>
                          <option value="operator" {{ $user->role === 'operator' ? 'selected' : '' }}>Operator</option>
                        </select>
                      </div>
                      <div class="mb-3"><label class="form-label">Password Baru (opsional)</label><input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak diganti" /></div>
                      <div class="form-check"><input type="checkbox" class="form-check-input" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }} /><label class="form-check-label">Aktif</label></div>
                    </div>
                    <div class="modal-footer"><button type="submit" class="btn btn-primary">Simpan</button></div>
                  </form>
                </div>
              </div>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

