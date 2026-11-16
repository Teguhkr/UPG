@extends('layouts/layoutMaster')

@section('title', 'Kelola Infografis, Video & Galeri')

@section('content')
@if(session('success'))
  <div class="alert alert-success alert-dismissible">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card">
  <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <div class="d-flex align-items-center gap-2">
      <h5 class="mb-0 me-3">Media Edukasi</h5>
      <a href="{{ route('admin.media.index') }}" class="btn btn-sm {{ !$type ? 'btn-primary' : 'btn-label-primary' }}">Semua</a>
      <a href="{{ route('admin.media.index', ['type' => 'infografis']) }}" class="btn btn-sm {{ $type === 'infografis' ? 'btn-primary' : 'btn-label-primary' }}">Infografis</a>
      <a href="{{ route('admin.media.index', ['type' => 'video']) }}" class="btn btn-sm {{ $type === 'video' ? 'btn-primary' : 'btn-label-primary' }}">Video</a>
      <a href="{{ route('admin.media.index', ['type' => 'gallery']) }}" class="btn btn-sm {{ $type === 'gallery' ? 'btn-primary' : 'btn-label-primary' }}">Galeri</a>
    </div>
    <a href="{{ route('admin.media.create') }}" class="btn btn-primary">
      <i class="icon-base ti tabler-plus me-1"></i> Tambah Media
    </a>
  </div>
  <div class="card-body">
    <div class="row g-4">
      @forelse($items as $item)
        <div class="col-6 col-md-4 col-lg-3">
          <div class="card h-100 border">
            @if($item->type === 'video')
              <div class="bg-dark text-white d-flex align-items-center justify-content-center" style="height: 150px;">
                @if($item->poster_url)
                  <img src="{{ $item->poster_url }}" class="w-100 h-100 object-fit-cover" alt="{{ $item->title }}" />
                @else
                  <i class="icon-base ti tabler-video icon-xl"></i>
                @endif
              </div>
            @elseif($item->file_url)
              <img src="{{ $item->file_url }}" class="card-img-top object-fit-cover" style="height: 150px;" alt="{{ $item->title }}" />
            @endif
            <div class="card-body p-3 d-flex flex-column">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="badge bg-label-primary">{{ ucfirst($item->type) }}</span>
                <span class="badge bg-label-{{ $item->is_published ? 'success' : 'secondary' }}">{{ $item->is_published ? 'Terbit' : 'Draft' }}</span>
              </div>
              <h6 class="small fw-semibold mb-2 flex-grow-1">{{ $item->title }}</h6>
              <div class="d-flex gap-2">
                <a href="{{ route('admin.media.edit', $item) }}" class="btn btn-sm btn-label-primary flex-grow-1">Edit</a>
                <form action="{{ route('admin.media.destroy', $item) }}" method="POST" onsubmit="return confirm('Hapus media ini?')">
                  @csrf @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-label-danger"><i class="icon-base ti tabler-trash"></i></button>
                </form>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12 text-center py-6 text-body-secondary">Belum ada item media.</div>
      @endforelse
    </div>
  </div>
  @if($items->hasPages())
    <div class="card-footer">{{ $items->links() }}</div>
  @endif
</div>
@endsection

