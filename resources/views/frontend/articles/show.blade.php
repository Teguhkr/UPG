@php
  $pageConfigs = ['myLayout' => 'front'];
@endphp

@extends('layouts/layoutMaster')

@section('title', $article->title)

@section('content')
<section class="section-py bg-body-secondary" style="padding-top: 7rem;">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        @if($article->category)
          <li class="breadcrumb-item">{{ $article->category->name }}</li>
        @endif
        <li class="breadcrumb-item active">{{ \Illuminate\Support\Str::limit($article->title, 50) }}</li>
      </ol>
    </nav>

    <div class="row g-6">
      <div class="col-lg-8">
        <div class="card shadow-sm">
          <div class="card-body p-6 p-md-8">
            @if($article->category)
              <span class="badge bg-label-primary mb-3">{{ $article->category->name }}</span>
            @endif
            <h2 class="fw-bold mb-3">{{ $article->title }}</h2>
            <div class="d-flex flex-wrap gap-4 text-body-secondary small mb-5 pb-4 border-bottom">
              <span><i class="icon-base ti tabler-calendar me-1"></i>{{ optional($article->published_at)->translatedFormat('d F Y') }}</span>
              <span><i class="icon-base ti tabler-eye me-1"></i>{{ number_format($article->hits) }} kali dibaca</span>
            </div>

            <div class="upg-prose">
              {!! $article->content !!}
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-4">
        <div class="card shadow-sm mb-6 bg-primary text-white">
          <div class="card-body text-center p-6">
            <h5 class="text-white fw-bold mb-2">Lapor Gratifikasi Online</h5>
            <p class="small mb-4 opacity-75">Laporkan penerimaan atau penolakan gratifikasi melalui aplikasi resmi GOL KPK RI.</p>
            <a href="{{ $siteSettings['gol_url'] ?? 'https://gol.kpk.go.id' }}" target="_blank" rel="noopener noreferrer" class="btn btn-light text-primary fw-semibold w-100">
              Buka GOL KPK <i class="icon-base ti tabler-external-link ms-1"></i>
            </a>
          </div>
        </div>

        @if($related->isNotEmpty())
          <div class="card shadow-sm">
            <div class="card-header">
              <h6 class="mb-0">Artikel Terkait</h6>
            </div>
            <div class="list-group list-group-flush">
              @foreach($related as $item)
                <a href="{{ route('articles.show', $item->slug) }}" class="list-group-item list-group-item-action">
                  <div class="fw-medium small mb-1">{{ $item->title }}</div>
                  <small class="text-body-secondary">{{ optional($item->published_at)->translatedFormat('d M Y') }}</small>
                </a>
              @endforeach
            </div>
          </div>
        @endif
      </div>
    </div>
  </div>
</section>
@endsection

