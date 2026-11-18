@extends('layouts/layoutMaster')

@section('title', 'Pengguna Admin')

@section('vendor-style')
  @vite('resources/assets/vendor/libs/select2/select2.scss')
@endsection

@section('vendor-script')
  @vite('resources/assets/vendor/libs/select2/select2.js')
@endsection

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
            <input type="text" name="name" value="{{ old('name') }}" class="form-control" required />
            @error('name')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" name="username" value="{{ old('username') }}" class="form-control" required />
            @error('username')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" class="form-control" required />
            @error('email')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3" data-user-org>
            <label class="form-label">Eselon</label>
            <select name="eselonering_id" class="form-select js-user-eselon" required>
              <option value="">Pilih eselon</option>
              @foreach($eseloneringRows as $eselon)
                <option value="{{ $eselon->id }}" @selected(old('eselonering_id') == $eselon->id)>{{ $eselon->nama_eselon ?: $eselon->eselonering }}</option>
              @endforeach
            </select>
            @error('eselonering_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            <label class="form-label mt-3">Satuan Kerja</label>
            <select name="satker_id" class="form-select js-user-satker" required>
              <option value="">Pilih satuan kerja</option>
              @foreach($satkers as $satker)
                <option value="{{ $satker->id }}" data-eselon-id="{{ $satker->eselonering }}" @selected(old('satker_id') == $satker->id)>{{ $satker->list_satker }}</option>
              @endforeach
            </select>
            @error('satker_id')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label">Role</label>
            <select name="role" class="form-select" required>
              <option value="super_admin" @selected(old('role') === 'super_admin')>Super Admin</option>
              <option value="editor" @selected(old('role', 'editor') === 'editor')>Editor</option>
              <option value="operator" @selected(old('role') === 'operator')>Operator</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" name="password" class="form-control" minlength="6" required />
            @error('password')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="form-check mb-4">
            <input type="checkbox" class="form-check-input" id="u_active" name="is_active" value="1" @checked(old('is_active', '1')) />
            <label class="form-check-label" for="u_active">Akun Aktif</label>
          </div>
          <button type="submit" class="btn btn-primary w-100">Tambah Pengguna</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card">
      <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
        <h5 class="mb-0">Daftar Pengguna Admin</h5>
        <form method="GET" action="{{ route('admin.users.index') }}" class="d-flex gap-2">
          <input type="search" name="search" value="{{ $search }}" class="form-control" placeholder="Cari pengguna, eselon, satker..." aria-label="Cari pengguna" />
          <button type="submit" class="btn btn-primary" title="Cari"><i class="icon-base ti tabler-search"></i></button>
          @if($search !== '')
            <a href="{{ route('admin.users.index') }}" class="btn btn-label-secondary" title="Hapus pencarian"><i class="icon-base ti tabler-x"></i></a>
          @endif
        </form>
      </div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Nama / Username</th>
              <th>Eselon</th>
              <th>Satuan Kerja</th>
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
                <td>{{ $user->satker?->eselon?->nama_eselon ?: ($user->satker?->eselon?->eselonering ?? '-') }}</td>
                <td>{{ $user->satker?->list_satker ?? '-' }}</td>
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
            @endforeach
            @if($users->isEmpty())
              <tr><td colspan="7" class="text-center text-body-secondary py-5">Tidak ada pengguna yang cocok dengan pencarian.</td></tr>
            @endif
          </tbody>
        </table>
      </div>
      @foreach($users as $user)
        <div class="modal fade" id="editUserModal{{ $user->id }}" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog">
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="modal-content bg-body">
              @csrf @method('PUT')
              <div class="modal-header">
                <h5 class="modal-title">Edit Pengguna</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body">
                <div class="mb-3"><label class="form-label">Nama</label><input type="text" name="name" value="{{ $user->name }}" class="form-control" required /></div>
                <div class="mb-3"><label class="form-label">Username</label><input type="text" name="username" value="{{ $user->username }}" class="form-control" required /></div>
                <div class="mb-3"><label class="form-label">Email</label><input type="email" name="email" value="{{ $user->email }}" class="form-control" required /></div>
                <div class="mb-3" data-user-org>
                  <label class="form-label">Eselon</label>
                  <select name="eselonering_id" class="form-select js-user-eselon" required>
                    <option value="">Pilih eselon</option>
                    @foreach($eseloneringRows as $eselon)
                      <option value="{{ $eselon->id }}" @selected($user->satker?->eselonering == $eselon->id)>{{ $eselon->nama_eselon ?: $eselon->eselonering }}</option>
                    @endforeach
                  </select>
                  <label class="form-label mt-3">Satuan Kerja</label>
                  <select name="satker_id" class="form-select js-user-satker" required>
                    <option value="">Pilih satuan kerja</option>
                    @foreach($satkers as $satker)
                      <option value="{{ $satker->id }}" data-eselon-id="{{ $satker->eselonering }}" @selected($user->satker_id == $satker->id)>{{ $satker->list_satker }}</option>
                    @endforeach
                  </select>
                </div>
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
      @if($users->hasPages())
        <div class="card-footer">{{ $users->links() }}</div>
      @endif
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script>
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-user-org]').forEach((group) => {
      const eselonSelect = group.querySelector('.js-user-eselon');
      const satkerSelect = group.querySelector('.js-user-satker');
      const modal = $(group).closest('.modal');
      const select2Options = {
        minimumResultsForSearch: 0,
        placeholder: 'Ketik untuk mencari...',
        width: '100%'
      };

      if (modal.length) {
        select2Options.dropdownParent = modal;
      }

      $(eselonSelect).select2(select2Options);
      $(satkerSelect).select2({
        ...select2Options,
        matcher: (params, data) => {
          if (!data.element || data.id === '') {
            return data;
          }

          if (data.element.dataset.eselonId !== eselonSelect.value) {
            return null;
          }

          const searchTerm = String(params.term || '').trim().toLowerCase();
          return !searchTerm || data.text.toLowerCase().includes(searchTerm) ? data : null;
        }
      });

      eselonSelect.addEventListener('change', () => {
        satkerSelect.value = '';
        $(satkerSelect).trigger('change');
      });
    });
  });
</script>
@endsection

