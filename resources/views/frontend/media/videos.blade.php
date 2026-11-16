@php
  $pageConfigs = ['myLayout' => 'front'];
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Video Gratifikasi')

@section('content')
<section class="section-py bg-body-secondary" style="padding-top: 7rem;">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item">Publikasi</li>
        <li class="breadcrumb-item active">Video Gratifikasi</li>
      </ol>
    </nav>

    <div class="mb-6">
      <h2 class="fw-bold mb-1">Video Edukasi &amp; Sosialisasi Gratifikasi</h2>
      <p class="text-body-secondary mb-0">Materi video layanan masyarakat, e-learning, dan pemahaman pengendalian gratifikasi dari KKP dan KPK RI.</p>
    </div>

    <div class="row g-6">
      @forelse($items as $video)
        <div class="col-md-6">
          <div class="card h-100 shadow-sm">
            <div class="card-body">
              @if($video->file_url)
                <video class="w-100 rounded mb-4" controls preload="metadata" poster="{{ $video->poster_url }}" style="max-height: 320px; background: #000;">
                  <source src="{{ $video->file_url }}" type="video/mp4" />
                  Browser Anda tidak mendukung pemutar video HTML5.
                </video>
              @elseif($video->embed_url)
                <div class="ratio ratio-16x9 mb-4">
                  <iframe src="{{ $video->embed_url }}" allowfullscreen class="rounded"></iframe>
                </div>
              @endif
              <div class="d-flex justify-content-between align-items-start gap-2">
                <div>
                  <h5 class="card-title mb-2">{{ $video->title }}</h5>
                  <p class="card-text text-body-secondary small mb-0">{{ $video->description }}</p>
                </div>
                @if($video->file_url)
                  <a href="{{ $video->file_url }}" download class="btn btn-sm btn-label-primary flex-shrink-0" title="Unduh Video">
                    <i class="icon-base ti tabler-download"></i>
                  </a>
                @endif
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12">
          <div class="card"><div class="card-body text-center py-6">Belum ada video tersedia.</div></div>
        </div>
      @endforelse
    </div>
  </div>
</section>
@endsection

