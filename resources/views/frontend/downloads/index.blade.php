@php
  $pageConfigs = ['myLayout' => 'front'];
@endphp

@extends('layouts/layoutMaster')

@section('title', $activeCategory ? $activeCategory->name : 'Pusat Unduhan')

@section('content')
<section class="section-py bg-body-secondary" style="padding-top: 7rem;">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item active">Unduh Dokumen</li>
      </ol>
    </nav>

    <div class="row align-items-center justify-content-between g-4 mb-6">
      <div class="col-lg-7">
        <h2 class="fw-bold mb-1">{{ $activeCategory ? $activeCategory->name : 'Pusat Dokumen & Unduhan UPG KKP' }}</h2>
        <p class="text-body-secondary mb-0">Formulir pelaporan gratifikasi, peraturan perundang-undangan, buku saku, materi sosialisasi, dan panduan GOL.</p>
      </div>
      <div class="col-lg-4">
        <form method="GET" action="{{ url()->current() }}">
          <div class="input-group">
            <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Cari dokumen..." />
            <button class="btn btn-primary" type="submit"><i class="icon-base ti tabler-search"></i></button>
          </div>
        </form>
      </div>
    </div>

    <div class="row g-6">
      <div class="col-lg-3">
        <div class="card shadow-sm">
          <div class="card-header">
            <h6 class="mb-0">Kategori Dokumen</h6>
          </div>
          <div class="list-group list-group-flush">
            <a href="{{ route('downloads.index') }}"
              class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ !$activeCategory ? 'active' : '' }}">
              <span>Semua Kategori</span>
            </a>
            @foreach($categories as $cat)
              <a href="{{ route('downloads.category', $cat->slug) }}"
                class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ $activeCategory && $activeCategory->id === $cat->id ? 'active' : '' }}">
                <span class="me-2">{{ $cat->name }}</span>
                <span class="badge rounded-pill {{ $activeCategory && $activeCategory->id === $cat->id ? 'bg-white text-primary' : 'bg-label-primary' }}">
                  {{ $cat->downloads_count }}
                </span>
              </a>
            @endforeach
          </div>
        </div>
      </div>

      <div class="col-lg-9">
        <div class="card shadow-sm">
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>Dokumen</th>
                  <th>Kategori</th>
                  <th>Ukuran</th>
                  <th class="text-center">Diunduh</th>
                  <th class="text-end">Aksi</th>
                </tr>
              </thead>
              <tbody>
                @forelse($downloads as $dl)
                  <tr>
                    <td>
                      <div class="d-flex align-items-start gap-3">
                        <span class="badge bg-label-primary mt-1">{{ $dl->extension }}</span>
                        <div>
                          <div class="fw-semibold text-heading">{{ $dl->title }}</div>
                          @if($dl->description)
                            <small class="text-body-secondary d-block">{{ \Illuminate\Support\Str::limit($dl->description, 100) }}</small>
                          @endif
                        </div>
                      </div>
                    </td>
                    <td><small class="text-body-secondary">{{ $dl->category->name ?? '-' }}</small></td>
                    <td><small class="text-nowrap">{{ $dl->file_size ?: '-' }}</small></td>
                    <td class="text-center"><span class="badge bg-label-secondary">{{ number_format($dl->download_count) }}</span></td>
                    <td class="text-end">
                      <a href="{{ route('downloads.file', $dl->slug) }}"
                        @if($dl->external_url && !$dl->file_path) target="_blank" rel="noopener noreferrer" @endif
                        class="btn btn-sm btn-primary text-nowrap">
                        <i class="icon-base ti {{ $dl->external_url && !$dl->file_path ? 'tabler-external-link' : 'tabler-download' }} me-1"></i>
                        {{ $dl->external_url && !$dl->file_path ? 'Buka' : 'Unduh' }}
                      </a>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center py-6 text-body-secondary">Tidak ada dokumen ditemukan.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          @if($downloads->hasPages())
            <div class="card-footer d-flex justify-content-center">
              {{ $downloads->links() }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

