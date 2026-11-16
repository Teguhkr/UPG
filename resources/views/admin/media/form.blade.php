@extends('layouts/layoutMaster')

@section('title', $mediaItem->exists ? 'Edit Media' : 'Tambah Media')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">{{ $mediaItem->exists ? 'Edit Item Media' : 'Tambah Item Media' }}</h5>
    <a href="{{ route('admin.media.index') }}" class="btn btn-sm btn-label-secondary">Kembali</a>
  </div>
  <div class="card-body">
    @if($errors->any())
      <div class="alert alert-danger"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" enctype="multipart/form-data"
      action="{{ $mediaItem->exists ? route('admin.media.update', $mediaItem) : route('admin.media.store') }}">
      @csrf
      @if($mediaItem->exists) @method('PUT') @endif

      <div class="row g-4">
        <div class="col-md-4">
          <label class="form-label">Tipe Media <span class="text-danger">*</span></label>
          <select name="type" class="form-select" required>
            <option value="infografis" {{ old('type', $mediaItem->type) === 'infografis' ? 'selected' : '' }}>Infografis</option>
            <option value="video" {{ old('type', $mediaItem->type) === 'video' ? 'selected' : '' }}>Video</option>
            <option value="gallery" {{ old('type', $mediaItem->type) === 'gallery' ? 'selected' : '' }}>Galeri Kegiatan</option>
          </select>
        </div>
        <div class="col-md-8">
          <label class="form-label">Judul <span class="text-danger">*</span></label>
          <input type="text" name="title" value="{{ old('title', $mediaItem->title) }}" class="form-control" required />
        </div>
        <div class="col-12">
          <label class="form-label">Deskripsi</label>
          <textarea name="description" rows="2" class="form-control">{{ old('description', $mediaItem->description) }}</textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label">Unggah File (Gambar / MP4)</label>
          <input type="file" name="media_file" class="form-control" />
          @if($mediaItem->file_url)
            <small class="text-body-secondary">File saat ini: <a href="{{ $mediaItem->file_url }}" target="_blank">{{ basename($mediaItem->file_path) }}</a></small>
          @endif
        </div>
        <div class="col-md-6">
          <label class="form-label">URL Embed Video (Google Drive / YouTube, opsional)</label>
          <input type="url" name="embed_url" value="{{ old('embed_url', $mediaItem->embed_url) }}" class="form-control" placeholder="https://..." />
        </div>
        <div class="col-md-4">
          <label class="form-label">Poster Video (opsional)</label>
          <input type="file" name="poster_file" class="form-control" accept="image/*" />
        </div>
        <div class="col-md-4">
          <label class="form-label">Album / Grup</label>
          <input type="text" name="album" value="{{ old('album', $mediaItem->album) }}" class="form-control" />
        </div>
        <div class="col-md-2">
          <label class="form-label">Urutan</label>
          <input type="number" name="sort_order" value="{{ old('sort_order', $mediaItem->sort_order) }}" class="form-control" />
        </div>
        <div class="col-md-2 d-flex align-items-end pb-2">
          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="m_pub" name="is_published" value="1"
              {{ old('is_published', $mediaItem->is_published) ? 'checked' : '' }} />
            <label class="form-check-label" for="m_pub">Terbitkan</label>
          </div>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">
            <i class="icon-base ti tabler-device-floppy me-1"></i> Simpan Media
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

