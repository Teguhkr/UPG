@php
  $pageConfigs = ['myLayout' => 'front'];
@endphp

@extends('layouts/layoutMaster')

@section('title', $category->name)

@section('content')
<section class="section-py bg-body-secondary" style="padding-top: 7rem;">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item">Publikasi</li>
        <li class="breadcrumb-item active">{{ $category->name }}</li>
      </ol>
    </nav>

    <div class="row align-items-center justify-content-between g-4 mb-6">
      <div class="col-lg-7">
        <h2 class="fw-bold mb-1">{{ $category->name }}</h2>
        @if($category->description)
          <p class="text-body-secondary mb-0">{{ $category->description }}</p>
        @endif
      </div>
      <div class="col-lg-4">
        <form method="GET" action="{{ url()->current() }}">
          <div class="input-group">
            <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Cari judul atau ringkasan..." />
            <button class="btn btn-primary" type="submit"><i class="icon-base ti tabler-search"></i></button>
          </div>
        </form>
      </div>
    </div>

    @if($articles->isEmpty())
      <div class="card">
        <div class="card-body text-center py-8">
          <i class="icon-base ti tabler-news-off icon-xl text-body-secondary mb-3"></i>
          <h5>Belum ada artikel yang sesuai</h5>
          <p class="text-body-secondary mb-0">Coba ubah kata kunci pencarian Anda.</p>
        </div>
      </div>
    @else
      <div class="row g-6 mb-6">
        @foreach($articles as $article)
          <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
              @if($article->image_url)
                <img src="{{ $article->image_url }}" class="card-img-top object-fit-cover" alt="{{ $article->title }}" style="height: 200px;" />
              @else
                <div class="bg-label-primary d-flex align-items-center justify-content-center" style="height: 200px;">
                  <i class="icon-base ti tabler-article icon-xl"></i>
                </div>
              @endif
              <div class="card-body d-flex flex-column">
                <div class="d-flex justify-content-between align-items-center small text-body-secondary mb-2">
                  <span><i class="icon-base ti tabler-calendar me-1"></i>{{ optional($article->published_at)->translatedFormat('d M Y') }}</span>
                  <span><i class="icon-base ti tabler-eye me-1"></i>{{ number_format($article->hits) }}</span>
                </div>
                <h5 class="card-title mb-2">
                  <a href="{{ route('articles.show', $article->slug) }}" class="text-heading text-decoration-none">{{ $article->title }}</a>
                </h5>
                <p class="card-text text-body-secondary small flex-grow-1">{{ \Illuminate\Support\Str::limit($article->excerpt, 140) }}</p>
                <a href="{{ route('articles.show', $article->slug) }}" class="btn btn-sm btn-label-primary mt-3 align-self-start">
                  Baca Selengkapnya <i class="icon-base ti tabler-arrow-right ms-1"></i>
                </a>
              </div>
            </div>
          </div>
        @endforeach
      </div>

      <div class="d-flex justify-content-center">
        {{ $articles->links() }}
      </div>
    @endif
  </div>
</section>
@endsection

