@php
  $golUrl = $siteSettings['gol_url'] ?? 'https://gol.kpk.go.id';
  $kpkUrl = $siteSettings['kpk_gratifikasi_url'] ?? 'https://gratifikasi.kpk.go.id';
  $simulasiUrl = $siteSettings['kpk_simulasi_url'] ?? 'https://gratifikasi.kpk.go.id/?page_id=3212';
@endphp

<!-- Floating Quick Action Button -->
<div class="position-fixed bottom-0 end-0 p-4" style="z-index: 1040;">
  <div class="btn-group dropup shadow-lg rounded-pill">
    <button type="button" class="btn btn-primary rounded-pill px-4 py-2 dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
      <i class="icon-base ti tabler-bolt me-1"></i> Akses Cepat UPG
    </button>
    <ul class="dropdown-menu dropdown-menu-end mb-2 shadow">
      <li>
        <a class="dropdown-item text-danger fw-semibold" href="{{ $golUrl }}" target="_blank" rel="noopener noreferrer">
          <i class="icon-base ti tabler-alert-circle me-2"></i>Lapor Gratifikasi (GOL KPK)
        </a>
      </li>
      <li>
        <a class="dropdown-item" href="{{ route('contact.index') }}">
          <i class="icon-base ti tabler-mail me-2 text-success"></i>Konsultasi UPG KKP
        </a>
      </li>
      <li>
        <a class="dropdown-item" href="{{ route('publikasi.infografis') }}">
          <i class="icon-base ti tabler-photo me-2 text-primary"></i>Infografis Gratifikasi
        </a>
      </li>
      <li>
        <a class="dropdown-item" href="{{ route('downloads.index') }}">
          <i class="icon-base ti tabler-download me-2 text-warning"></i>Unduh Formulir &amp; Aturan
        </a>
      </li>
    </ul>
  </div>
</div>

<!-- Footer: Start -->
<footer class="landing-footer footer-text">
  <div class="footer-top position-relative overflow-hidden z-1">
    <div class="container">
      <div class="row gx-0 gy-6 g-lg-8">
        <div class="col-lg-3 col-md-6">
          <h6 class="footer-title mb-4">Profil &amp; Publikasi</h6>
          <ul class="list-unstyled">
            <li class="mb-3"><a href="{{ route('profil.tentang') }}" class="footer-link">Tentang Kami</a></li>
            <li class="mb-3"><a href="{{ route('contact.index') }}" class="footer-link">Hubungi &amp; Konsultasi UPG</a></li>
            <li class="mb-3"><a href="{{ route('publikasi.informasi') }}" class="footer-link">Informasi Seputar PPG</a></li>
            <li class="mb-3"><a href="{{ route('publikasi.infografis') }}" class="footer-link">Infografis Terkait PPG</a></li>
            <li class="mb-3"><a href="{{ route('publikasi.mengenal') }}" class="footer-link">Apa itu Gratifikasi?</a></li>
            <li><a href="{{ route('publikasi.videos') }}" class="footer-link">Video Materi Gratifikasi</a></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-6">
          <h6 class="footer-title mb-4">FAQ's &amp; Unduh</h6>
          <ul class="list-unstyled">
            <li class="mb-3"><a href="{{ route('faq.index') }}" class="footer-link">Hal Sering Ditanyakan (FAQ)</a></li>
            <li class="mb-3"><a href="{{ route('faq.cases') }}" class="footer-link">Contoh Kasus Gratifikasi</a></li>
            <li class="mb-3"><a href="{{ route('downloads.index') }}" class="footer-link">Pusat Dokumen Unduhan</a></li>
            <li class="mb-3"><a href="{{ route('pages.show', 'kebijakan-privasi') }}" class="footer-link">Kebijakan Privasi</a></li>
            <li><a href="{{ route('pages.show', 'ketentuan-penggunaan') }}" class="footer-link">Ketentuan Penggunaan</a></li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-6">
          <h6 class="footer-title mb-4">Pranala Luar</h6>
          <ul class="list-unstyled">
            <li class="mb-3">
              <a href="{{ $golUrl }}" target="_blank" rel="noopener noreferrer" class="footer-link">
                GOL KPK (Pelaporan Online) <i class="icon-base ti tabler-external-link icon-xs ms-1"></i>
              </a>
            </li>
            <li class="mb-3">
              <a href="{{ $kpkUrl }}" target="_blank" rel="noopener noreferrer" class="footer-link">
                Beranda Gratifikasi KPK <i class="icon-base ti tabler-external-link icon-xs ms-1"></i>
              </a>
            </li>
            <li class="mb-3">
              <a href="{{ $simulasiUrl }}" target="_blank" rel="noopener noreferrer" class="footer-link">
                Simulasi Laporan Gratifikasi <i class="icon-base ti tabler-external-link icon-xs ms-1"></i>
              </a>
            </li>
            <li>
              <a href="{{ route('admin.login') }}" class="footer-link">
                <i class="icon-base ti tabler-lock me-1"></i>Login Pengelola UPG
              </a>
            </li>
          </ul>
        </div>

        <div class="col-lg-3 col-md-6">
          <h6 class="footer-title mb-4">Kontak Kami</h6>
          <div class="mb-3 bg-white rounded p-2 d-inline-block">
            <img src="{{ asset('storage/upg-media/logo/logo-header-upg-2021.png') }}" alt="Logo UPG KKP" style="height: 44px; width: auto;" />
          </div>
          <p class="footer-text fw-semibold mb-1">{{ $siteSettings['office_name'] ?? 'Unit Pengendalian Gratifikasi KKP' }}</p>
          <p class="footer-text small mb-2">{!! nl2br(e($siteSettings['office_address'] ?? "Gedung Mina Bahari II Lantai 5\nJl. Medan Merdeka Timur No. 16, Jakarta Pusat 10110")) !!}</p>
          <p class="footer-text small mb-0">
            <i class="icon-base ti tabler-mail me-1"></i> {{ $siteSettings['contact_email'] ?? 'upg@kkp.go.id' }}
          </p>
        </div>
      </div>
    </div>
  </div>
  <div class="footer-bottom py-3 py-md-4">
    <div class="container d-flex flex-wrap justify-content-between align-items-center flex-md-row flex-column text-center text-md-start gap-2">
      <div>
        <span class="footer-bottom-text small">{{ $siteSettings['copyright_text'] ?? '© ' . date('Y') . ' Unit Pengendalian Gratifikasi Kementerian Kelautan dan Perikanan RI.' }}</span>
      </div>
      <div class="d-flex gap-3">
        @if(!empty($siteSettings['twitter_url']))
          <a href="{{ $siteSettings['twitter_url'] }}" class="footer-social-link" target="_blank" rel="noopener noreferrer" title="Twitter / X">
            <i class="icon-base ti tabler-brand-x icon-md"></i>
          </a>
        @endif
        @if(!empty($siteSettings['instagram_url']))
          <a href="{{ $siteSettings['instagram_url'] }}" class="footer-social-link" target="_blank" rel="noopener noreferrer" title="Instagram">
            <i class="icon-base ti tabler-brand-instagram icon-md"></i>
          </a>
        @endif
        @if(!empty($siteSettings['youtube_url']))
          <a href="{{ $siteSettings['youtube_url'] }}" class="footer-social-link" target="_blank" rel="noopener noreferrer" title="YouTube">
            <i class="icon-base ti tabler-brand-youtube icon-md"></i>
          </a>
        @endif
      </div>
    </div>
  </div>
</footer>
<!-- Footer: End -->
