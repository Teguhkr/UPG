@extends('layouts/layoutMaster')

@section('title', $download->exists ? 'Edit Dokumen' : 'Tambah Dokumen')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">{{ $download->exists ? 'Edit Dokumen Unduh' : 'Tambah Dokumen Unduh' }}</h5>
    <a href="{{ route('admin.downloads.index') }}" class="btn btn-sm btn-label-secondary">Kembali</a>
  </div>
  <div class="card-body">
    @if($errors->any())
      <div class="alert alert-danger"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" enctype="multipart/form-data"
      action="{{ $download->exists ? route('admin.downloads.update', $download) : route('admin.downloads.store') }}">
      @csrf
      @if($download->exists) @method('PUT') @endif

      <div class="row g-4">
        <div class="col-md-8">
          <label class="form-label">Judul Dokumen <span class="text-danger">*</span></label>
          <input type="text" name="title" value="{{ old('title', $download->title) }}" class="form-control" required />
        </div>
        <div class="col-md-4">
          <label class="form-label">Kategori <span class="text-danger">*</span></label>
          <select name="category_id" class="form-select" required>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}" {{ old('category_id', $download->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-12">
          <label class="form-label">Deskripsi Singkat</label>
          <textarea name="description" rows="2" class="form-control">{{ old('description', $download->description) }}</textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Unggah Berkas (PDF / Office / ZIP, maks 50MB)</label>
          <input type="file" name="file_upload" class="form-control" />
          @if($download->file_name)
            <small class="text-body-secondary">Berkas saat ini: <strong>{{ $download->file_name }}</strong> ({{ $download->file_size }})</small>
          @endif
        </div>
        <div class="col-md-6">
          <label class="form-label">Atau Tautan Eksternal (opsional)</label>
          <input type="url" name="external_url" value="{{ old('external_url', $download->external_url) }}" class="form-control" placeholder="https://..." />
        </div>
        <div class="col-md-4">
          <label class="form-label">Versi / Tahun (opsional)</label>
          <input type="text" name="version" value="{{ old('version', $download->version) }}" class="form-control" />
        </div>
        <div class="col-md-4 d-flex align-items-end pb-2">
          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="dl_pub" name="is_published" value="1"
              {{ old('is_published', $download->is_published) ? 'checked' : '' }} />
            <label class="form-check-label" for="dl_pub">Terbitkan</label>
          </div>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">
            <i class="icon-base ti tabler-device-floppy me-1"></i> Simpan Dokumen
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

