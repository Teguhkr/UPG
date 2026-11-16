@php
  $pageConfigs = ['myLayout' => 'front'];
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Infografis Gratifikasi')

@section('content')
<section class="section-py bg-body-secondary" style="padding-top: 7rem;">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item">Publikasi</li>
        <li class="breadcrumb-item active">Infografis</li>
      </ol>
    </nav>

    <div class="mb-6">
      <h2 class="fw-bold mb-1">Infografis Pengendalian Gratifikasi</h2>
      <p class="text-body-secondary mb-0">Kumpulan poster dan infografis edukasi pengendalian gratifikasi di lingkungan Kementerian Kelautan dan Perikanan.</p>
    </div>

    <div class="row g-6">
      @forelse($items as $item)
        <div class="col-6 col-md-4 col-lg-3">
          <div class="card h-100 shadow-sm overflow-hidden">
            <a href="javascript:void(0);" class="d-block" data-bs-toggle="modal" data-bs-target="#infografisModal"
              data-title="{{ $item->title }}" data-img="{{ $item->file_url }}">
              <img src="{{ $item->file_url }}" alt="{{ $item->title }}" class="card-img-top object-fit-cover" style="height: 260px;" />
            </a>
            <div class="card-body p-4 d-flex flex-column justify-content-between">
              <h6 class="card-title mb-2">{{ $item->title }}</h6>
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-label-primary flex-grow-1"
                  data-bs-toggle="modal" data-bs-target="#infografisModal"
                  data-title="{{ $item->title }}" data-img="{{ $item->file_url }}">
                  <i class="icon-base ti tabler-zoom-in me-1"></i>Lihat
                </button>
                <a href="{{ $item->file_url }}" download class="btn btn-sm btn-outline-secondary" title="Unduh Gambar">
                  <i class="icon-base ti tabler-download"></i>
                </a>
              </div>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12">
          <div class="card"><div class="card-body text-center py-6">Belum ada infografis.</div></div>
        </div>
      @endforelse
    </div>
  </div>
</section>

<!-- Lightbox Modal -->
<div class="modal fade" id="infografisModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="infografisModalTitle">Infografis</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img id="infografisModalImg" src="" alt="" class="img-fluid rounded" />
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('infografisModal');
  if (modal) {
    modal.addEventListener('show.bs.modal', function (event) {
      const trigger = event.relatedTarget;
      document.getElementById('infografisModalTitle').textContent = trigger.getAttribute('data-title');
      document.getElementById('infografisModalImg').src = trigger.getAttribute('data-img');
    });
  }
});
</script>
@endsection

