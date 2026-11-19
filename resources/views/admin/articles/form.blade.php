@extends('layouts/layoutMaster')

@section('title', $article->exists ? 'Edit Artikel' : 'Tambah Artikel')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">{{ $article->exists ? 'Edit Artikel' : 'Tambah Artikel Baru' }}</h5>
    <a href="{{ route('admin.articles.index') }}" class="btn btn-sm btn-label-secondary">Kembali</a>
  </div>
  <div class="card-body">
    @if($errors->any())
      <div class="alert alert-danger"><ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form method="POST" enctype="multipart/form-data"
      action="{{ $article->exists ? route('admin.articles.update', $article) : route('admin.articles.store') }}">
      @csrf
      @if($article->exists) @method('PUT') @endif

      <div class="row g-4">
        <div class="col-md-8">
          <label class="form-label">Judul Artikel <span class="text-danger">*</span></label>
          <input type="text" name="title" value="{{ old('title', $article->title) }}" class="form-control" required />
        </div>
        <div class="col-md-4">
          <label class="form-label">Kategori <span class="text-danger">*</span></label>
          <select name="category_id" class="form-select" required>
            @foreach($categories as $cat)
              <option value="{{ $cat->id }}" {{ old('category_id', $article->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="col-md-6">
          <label class="form-label">Slug URL (opsional)</label>
          <input type="text" name="slug" value="{{ old('slug', $article->slug) }}" class="form-control" placeholder="otomatis dari judul jika kosong" />
        </div>
        <div class="col-md-3">
          <label class="form-label">Tanggal Terbit</label>
          <input type="datetime-local" name="published_at"
            value="{{ old('published_at', optional($article->published_at)->format('Y-m-d\TH:i')) }}" class="form-control" />
        </div>
        <div class="col-md-3 d-flex align-items-end gap-4 pb-2">
          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="is_published" name="is_published" value="1"
              {{ old('is_published', $article->is_published) ? 'checked' : '' }} />
            <label class="form-check-label" for="is_published">Terbitkan</label>
          </div>
          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1"
              {{ old('is_featured', $article->is_featured) ? 'checked' : '' }} />
            <label class="form-check-label" for="is_featured">Utama</label>
          </div>
        </div>

        <div class="col-12">
          <label class="form-label">Ringkasan (Excerpt)</label>
          <textarea name="excerpt" rows="2" class="form-control" placeholder="Kosongkan untuk generate otomatis dari isi artikel">{{ old('excerpt', $article->excerpt) }}</textarea>
        </div>

        <div class="col-12">
          <label class="form-label">Isi Konten (HTML)</label>
          <textarea name="content" rows="14" class="form-control font-monospace">{{ old('content', $article->content) }}</textarea>
        </div>

        <div class="col-md-6">
          <label class="form-label">Gambar Sampul</label>
          <input type="file" name="featured_image_file" class="form-control" accept="image/*" />
          @if($article->image_url)
            <img src="{{ $article->image_url }}" alt="" class="mt-2 rounded" style="max-height: 120px;" />
            <button type="submit" form="deleteFeaturedImageForm" class="btn btn-outline-danger btn-sm mt-2">
              <i class="icon-base ti tabler-trash me-1"></i> Hapus
            </button>
          @endif
        </div>
        <div class="col-md-6">
          <label class="form-label">Meta Keywords</label>
          <input type="text" name="meta_keywords" value="{{ old('meta_keywords', $article->meta_keywords) }}" class="form-control" />
        </div>

        <div class="col-12">
          <button type="submit" class="btn btn-primary">
            <i class="icon-base ti tabler-device-floppy me-1"></i> Simpan Artikel
          </button>
        </div>
      </div>
    </form>
    @if($article->exists && $article->image_url)
      <form id="deleteFeaturedImageForm" method="POST" action="{{ route('admin.articles.featured-image.destroy', $article) }}"
        onsubmit="return confirm('Hapus gambar sampul artikel ini?')">
        @csrf
        @method('DELETE')
      </form>
    @endif
  </div>
</div>
@endsection

