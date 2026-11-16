@php
  $pageConfigs = ['myLayout' => 'front'];
@endphp

@extends('layouts/layoutMaster')

@section('title', $pageTitle)

@section('content')
<section class="section-py bg-body-secondary" style="padding-top: 7rem;">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item">FAQ's</li>
        <li class="breadcrumb-item active">{{ $mode === 'contoh_kasus' ? 'Contoh Kasus' : "FAQ's" }}</li>
      </ol>
    </nav>

    <div class="row align-items-center justify-content-between g-4 mb-6">
      <div class="col-lg-7">
        <h2 class="fw-bold mb-1">{{ $pageTitle }}</h2>
        <p class="text-body-secondary mb-0">{{ $pageSubtitle }}</p>
      </div>
      <div class="col-lg-4">
        <form method="GET" action="{{ url()->current() }}">
          <div class="input-group">
            <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Cari pertanyaan atau topik..." />
            <button class="btn btn-primary" type="submit"><i class="icon-base ti tabler-search"></i></button>
          </div>
        </form>
      </div>
    </div>

    <div class="mb-5 d-flex gap-2">
      <a href="{{ route('faq.index') }}" class="btn {{ $mode === 'faq' ? 'btn-primary' : 'btn-label-primary' }}">
        <i class="icon-base ti tabler-help-circle me-1"></i> FAQ's
      </a>
      <a href="{{ route('faq.cases') }}" class="btn {{ $mode === 'contoh_kasus' ? 'btn-primary' : 'btn-label-primary' }}">
        <i class="icon-base ti tabler-scale me-1"></i> Contoh Kasus
      </a>
    </div>

    @if($items->isEmpty())
      <div class="card">
        <div class="card-body text-center py-6">
          <p class="text-body-secondary mb-0">Belum ada data yang sesuai dengan pencarian Anda.</p>
        </div>
      </div>
    @else
      <div class="accordion shadow-sm" id="faqPageAccordion">
        @foreach($items as $idx => $item)
          <div class="accordion-item">
            <h2 class="accordion-header" id="qHeading{{ $item->id }}">
              <button class="accordion-button {{ $idx > 0 ? 'collapsed' : '' }} fw-medium" type="button"
                data-bs-toggle="collapse" data-bs-target="#qCollapse{{ $item->id }}"
                aria-expanded="{{ $idx === 0 ? 'true' : 'false' }}" aria-controls="qCollapse{{ $item->id }}">
                <span class="badge bg-label-primary me-3">{{ $idx + 1 }}</span>
                {{ $item->question }}
              </button>
            </h2>
            <div id="qCollapse{{ $item->id }}" class="accordion-collapse collapse {{ $idx === 0 ? 'show' : '' }}"
              aria-labelledby="qHeading{{ $item->id }}" data-bs-parent="#faqPageAccordion">
              <div class="accordion-body upg-prose">
                {!! $item->answer !!}
              </div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</section>
@endsection

