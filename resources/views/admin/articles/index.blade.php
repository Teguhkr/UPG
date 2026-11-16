@extends('layouts/layoutMaster')

@section('title', 'Kelola Artikel & Berita')

@section('content')
@if(session('success'))
  <div class="alert alert-success alert-dismissible">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card">
  <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <h5 class="mb-0">Daftar Artikel &amp; Berita</h5>
    <a href="{{ route('admin.articles.create') }}" class="btn btn-primary">
      <i class="icon-base ti tabler-plus me-1"></i> Tambah Artikel
    </a>
  </div>
  <div class="card-body border-bottom">
    <form method="GET" class="row g-3">
      <div class="col-md-4">
        <select name="category_id" class="form-select" onchange="this.form.submit()">
          <option value="">-- Semua Kategori --</option>
          @foreach($categories as $cat)
            <option value="{{ $cat->id }}" {{ (string)$categoryId === (string)$cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-5">
        <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Cari judul artikel..." />
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-label-primary" type="submit">Filter</button>
        <a href="{{ route('admin.articles.index') }}" class="btn btn-label-secondary">Reset</a>
      </div>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Judul</th>
          <th>Kategori</th>
          <th>Status</th>
          <th>Dibaca</th>
          <th>Tanggal</th>
          <th class="text-end">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($articles as $article)
          <tr>
            <td>
              <div class="fw-semibold">{{ $article->title }}</div>
              @if($article->is_featured)
                <span class="badge bg-label-warning">Utama</span>
              @endif
            </td>
            <td>{{ $article->category->name ?? '-' }}</td>
            <td>
              <span class="badge {{ $article->is_published ? 'bg-label-success' : 'bg-label-secondary' }}">
                {{ $article->is_published ? 'Terbit' : 'Draft' }}
              </span>
            </td>
            <td>{{ number_format($article->hits) }}</td>
            <td><small>{{ optional($article->published_at)->format('d/m/Y') }}</small></td>
            <td class="text-end text-nowrap">
              <a href="{{ route('articles.show', $article->slug) }}" target="_blank" class="btn btn-sm btn-icon btn-label-info" title="Lihat">
                <i class="icon-base ti tabler-eye"></i>
              </a>
              <a href="{{ route('admin.articles.edit', $article) }}" class="btn btn-sm btn-icon btn-label-primary" title="Edit">
                <i class="icon-base ti tabler-edit"></i>
              </a>
              <form action="{{ route('admin.articles.destroy', $article) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus artikel ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-icon btn-label-danger" title="Hapus">
                  <i class="icon-base ti tabler-trash"></i>
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="text-center py-6 text-body-secondary">Belum ada artikel.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($articles->hasPages())
    <div class="card-footer">{{ $articles->links() }}</div>
  @endif
</div>
@endsection

