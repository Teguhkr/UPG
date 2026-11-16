@php
  $pageConfigs = ['myLayout' => 'front'];
@endphp

@extends('layouts/layoutMaster')

@section('title', $pageTitle)

@section('content')
<section class="section-py bg-body-secondary" style="padding-top: 7rem;">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-4">
      <ol class="breadcrumb">
        @foreach($breadcrumbs as $bc)
          @if($bc['url'])
            <li class="breadcrumb-item"><a href="{{ $bc['url'] }}">{{ $bc['label'] }}</a></li>
          @else
            <li class="breadcrumb-item active">{{ $bc['label'] }}</li>
          @endif
        @endforeach
      </ol>
    </nav>

    <div class="card shadow-sm">
      <div class="card-body p-6 p-md-8">
        <h2 class="fw-bold mb-4">{{ $article->title }}</h2>
        @if($article->image_url)
          <div class="mb-5">
            <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="img-fluid rounded shadow-sm w-100 object-fit-cover" style="max-height: 460px;" />
          </div>
        @endif
        <div class="upg-prose">
          {!! $article->content !!}
        </div>
      </div>
    </div>

    @if(isset($gallery) && $gallery->isNotEmpty())
      <div class="mt-8">
        <h4 class="fw-bold mb-4">Dokumentasi Kegiatan Penanaman Nilai Integritas</h4>
        <div class="row g-4">
          @foreach($gallery as $photo)
            <div class="col-6 col-md-3">
              <div class="card h-100 shadow-sm overflow-hidden">
                <img src="{{ $photo->file_url }}" alt="{{ $photo->title }}" class="img-fluid object-fit-cover" style="height: 170px;" />
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @endif
  </div>
</section>
@endsection

