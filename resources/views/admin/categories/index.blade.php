@extends('layouts/layoutMaster')

@section('title', 'Kelola Kategori')

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
      <div class="card-header"><h5 class="mb-0">Tambah Kategori</h5></div>
      <div class="card-body">
        <form method="POST" action="{{ route('admin.categories.store') }}">
          @csrf
          <div class="mb-4">
            <label class="form-label">Nama Kategori <span class="text-danger">*</span></label>
            <input type="text" name="name" class="form-control" required />
          </div>
          <div class="mb-4">
            <label class="form-label">Tipe <span class="text-danger">*</span></label>
            <select name="type" class="form-select" required>
              <option value="article">Artikel / Publikasi</option>
              <option value="download">Dokumen Unduh</option>
            </select>
          </div>
          <div class="mb-4">
            <label class="form-label">Urutan</label>
            <input type="number" name="sort_order" value="0" class="form-control" />
          </div>
          <div class="mb-4">
            <label class="form-label">Deskripsi</label>
            <textarea name="description" rows="2" class="form-control"></textarea>
          </div>
          <div class="form-check mb-4">
            <input type="checkbox" class="form-check-input" id="cat_pub" name="is_published" value="1" checked />
            <label class="form-check-label" for="cat_pub">Aktif / Terbitkan</label>
          </div>
          <button type="submit" class="btn btn-primary w-100">Simpan Kategori</button>
        </form>
      </div>
    </div>
  </div>

  <div class="col-lg-8">
    <div class="card">
      <div class="card-header"><h5 class="mb-0">Daftar Kategori</h5></div>
      <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Nama</th>
              <th>Tipe</th>
              <th>Jumlah Item</th>
              <th>Status</th>
              <th class="text-end">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($categories as $cat)
              <tr>
                <td>
                  <div class="fw-semibold">{{ $cat->name }}</div>
                  <small class="text-body-secondary">/{{ $cat->slug }}</small>
                </td>
                <td>
                  <span class="badge bg-label-{{ $cat->type === 'article' ? 'primary' : 'info' }}">
                    {{ $cat->type === 'article' ? 'Artikel' : 'Unduh' }}
                  </span>
                </td>
                <td>{{ $cat->type === 'article' ? $cat->articles_count : $cat->downloads_count }}</td>
                <td>
                  <span class="badge bg-label-{{ $cat->is_published ? 'success' : 'secondary' }}">
                    {{ $cat->is_published ? 'Aktif' : 'Nonaktif' }}
                  </span>
                </td>
                <td class="text-end text-nowrap">
                  <button type="button" class="btn btn-sm btn-icon btn-label-primary" data-bs-toggle="modal"
                    data-bs-target="#editCatModal{{ $cat->id }}"><i class="icon-base ti tabler-edit"></i></button>
                  <form action="{{ route('admin.categories.destroy', $cat) }}" method="POST" class="d-inline"
                    onsubmit="return confirm('Hapus kategori ini?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-icon btn-label-danger"><i class="icon-base ti tabler-trash"></i></button>
                  </form>
                </td>
              </tr>

              <!-- Edit Modal -->
              <div class="modal fade" id="editCatModal{{ $cat->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                  <form method="POST" action="{{ route('admin.categories.update', $cat) }}" class="modal-content">
                    @csrf @method('PUT')
                    <div class="modal-header">
                      <h5 class="modal-title">Edit Kategori</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input type="text" name="name" value="{{ $cat->name }}" class="form-control" required />
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Tipe</label>
                        <select name="type" class="form-select">
                          <option value="article" {{ $cat->type === 'article' ? 'selected' : '' }}>Artikel</option>
                          <option value="download" {{ $cat->type === 'download' ? 'selected' : '' }}>Unduh</option>
                        </select>
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Urutan</label>
                        <input type="number" name="sort_order" value="{{ $cat->sort_order }}" class="form-control" />
                      </div>
                      <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" rows="2" class="form-control">{{ $cat->description }}</textarea>
                      </div>
                      <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="is_published" value="1" {{ $cat->is_published ? 'checked' : '' }} />
                        <label class="form-check-label">Aktif</label>
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
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

