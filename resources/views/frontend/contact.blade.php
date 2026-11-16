@php
  $pageConfigs = ['myLayout' => 'front'];
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Hubungi & Konsultasi UPG KKP')

@section('content')
<section class="section-py bg-body-secondary" style="padding-top: 7rem;">
  <div class="container">
    <nav aria-label="breadcrumb" class="mb-3">
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
        <li class="breadcrumb-item">Profil</li>
        <li class="breadcrumb-item active">Hubungi UPG KKP</li>
      </ol>
    </nav>

    <div class="row g-6">
      <div class="col-lg-5">
        <div class="card shadow-sm h-100">
          <div class="card-body p-6">
            <img src="{{ asset('storage/upg-media/logo/logo-header-upg-2021.png') }}" alt="UPG KKP" class="mb-4" style="height: 54px;" />
            <h4 class="fw-bold mb-2">{{ $siteSettings['office_name'] ?? 'Unit Pengendalian Gratifikasi KKP' }}</h4>
            <p class="text-body-secondary mb-5">Silakan hubungi atau sampaikan konsultasi Anda terkait pengendalian gratifikasi di lingkungan Kementerian Kelautan dan Perikanan.</p>

            <div class="d-flex align-items-start gap-3 mb-4">
              <div class="avatar avatar-sm flex-shrink-0">
                <span class="avatar-initial rounded bg-label-primary"><i class="icon-base ti tabler-map-pin"></i></span>
              </div>
              <div>
                <h6 class="mb-1">Alamat Kantor</h6>
                <p class="text-body-secondary small mb-0">{!! nl2br(e($siteSettings['office_address'] ?? "Gedung Mina Bahari II Lantai 5\nJl. Medan Merdeka Timur No. 16, Jakarta Pusat 10110")) !!}</p>
              </div>
            </div>

            <div class="d-flex align-items-start gap-3 mb-4">
              <div class="avatar avatar-sm flex-shrink-0">
                <span class="avatar-initial rounded bg-label-primary"><i class="icon-base ti tabler-mail"></i></span>
              </div>
              <div>
                <h6 class="mb-1">Email Resmi</h6>
                <p class="text-body-secondary small mb-0">{{ $siteSettings['contact_email'] ?? 'upg@kkp.go.id' }}</p>
              </div>
            </div>

            <div class="d-flex align-items-start gap-3">
              <div class="avatar avatar-sm flex-shrink-0">
                <span class="avatar-initial rounded bg-label-primary"><i class="icon-base ti tabler-phone"></i></span>
              </div>
              <div>
                <h6 class="mb-1">Telepon</h6>
                <p class="text-body-secondary small mb-0">{{ $siteSettings['contact_phone'] ?? '(021) 3519070' }}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-7" id="display-form">
        <div class="card shadow-sm">
          <div class="card-header">
            <h5 class="mb-0"><i class="icon-base ti tabler-message-circle me-2 text-primary"></i>Formulir Konsultasi Gratifikasi</h5>
          </div>
          <div class="card-body p-6">
            @if(session('success'))
              <div class="alert alert-success alert-dismissible" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
            @endif

            @if($errors->any())
              <div class="alert alert-danger">
                <ul class="mb-0 ps-3">
                  @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <form method="POST" action="{{ route('contact.store') }}">
              @csrf
              <div class="row g-4">
                <div class="col-md-6">
                  <label class="form-label" for="name">Nama Lengkap <span class="text-danger">*</span></label>
                  <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control" required />
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="email">Email <span class="text-danger">*</span></label>
                  <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control" required />
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="unit_kerja">Unit Kerja / Instansi</label>
                  <input type="text" id="unit_kerja" name="unit_kerja" value="{{ old('unit_kerja') }}" class="form-control" placeholder="Opsional" />
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="phone">No. Telepon / WhatsApp</label>
                  <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="form-control" placeholder="Opsional" />
                </div>
                <div class="col-12">
                  <label class="form-label" for="subject">Subjek / Topik Konsultasi <span class="text-danger">*</span></label>
                  <input type="text" id="subject" name="subject" value="{{ old('subject') }}" class="form-control" required />
                </div>
                <div class="col-12">
                  <label class="form-label" for="message">Isi Pertanyaan / Konsultasi <span class="text-danger">*</span></label>
                  <textarea id="message" name="message" rows="5" class="form-control" required>{{ old('message') }}</textarea>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-primary">
                    <i class="icon-base ti tabler-send me-2"></i>Kirim Konsultasi
                  </button>
                </div>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection

