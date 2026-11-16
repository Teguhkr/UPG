@php
  $pageConfigs = ['myLayout' => 'front'];
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Pencarian')

@section('content')
<section class="section-py bg-body-secondary" style="padding-top: 7rem;">
  <div class="container">
    <h2 class="fw-bold mb-4">Hasil Pencarian</h2>

    <form action="{{ route('search') }}" method="GET" class="mb-6">
      <div class="input-group input-group-lg shadow-sm">
        <input type="text" name="q" value="{{ $query }}" class="form-control" placeholder="Ketik kata kunci artikel, dokumen unduh, atau FAQ..." />
        <button class="btn btn-primary" type="submit"><i class="icon-base ti tabler-search me-1"></i> Cari</button>
      </div>
    </form>

    @if($query === '')
      <div class="card"><div class="card-body text-center py-6 text-body-secondary">Masukkan kata kunci di atas untuk mencari informasi di portal UPG KKP.</div></div>
    @else
      <div class="row g-6">
        <div class="col-lg-6">
          <div class="card shadow-sm h-100">
            <div class="card-header"><h6 class="mb-0">Artikel &amp; Informasi ({{ $articles->count() }})</h6></div>
            <div class="list-group list-group-flush">
              @forelse($articles as $a)
                <a href="{{ route('articles.show', $a->slug) }}" class="list-group-item list-group-item-action">
                  <div class="fw-semibold">{{ $a->title }}</div>
                  <small class="text-body-secondary">{{ $a->category->name ?? '' }}</small>
                </a>
              @empty
                <div class="list-group-item text-body-secondary small">Tidak ada artikel yang cocok.</div>
              @endforelse
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="card shadow-sm h-100">
            <div class="card-header"><h6 class="mb-0">Dokumen Unduh ({{ $downloads->count() }})</h6></div>
            <div class="list-group list-group-flush">
              @forelse($downloads as $dl)
                <a href="{{ route('downloads.file', $dl->slug) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                  <div>
                    <div class="fw-semibold">{{ $dl->title }}</div>
                    <small class="text-body-secondary">{{ $dl->category->name ?? '' }}</small>
                  </div>
                  <span class="badge bg-label-primary">{{ $dl->extension }}</span>
                </a>
              @empty
                <div class="list-group-item text-body-secondary small">Tidak ada dokumen yang cocok.</div>
              @endforelse
            </div>
          </div>
        </div>

        <div class="col-12">
          <div class="card shadow-sm">
            <div class="card-header"><h6 class="mb-0">FAQ &amp; Contoh Kasus ({{ $faqs->count() }})</h6></div>
            <div class="list-group list-group-flush">
              @forelse($faqs as $f)
                <a href="{{ $f->category_type === 'contoh_kasus' ? route('faq.cases') : route('faq.index') }}" class="list-group-item list-group-item-action">
                  <span class="badge bg-label-secondary me-2">{{ $f->category_type === 'contoh_kasus' ? 'Contoh Kasus' : 'FAQ' }}</span>
                  <span class="fw-medium">{{ $f->question }}</span>
                </a>
              @empty
                <div class="list-group-item text-body-secondary small">Tidak ada FAQ yang cocok.</div>
              @endforelse
            </div>
          </div>
        </div>
      </div>
    @endif
  </div>
</section>
@endsection

