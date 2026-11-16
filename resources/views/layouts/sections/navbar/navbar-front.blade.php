@php
  use Illuminate\Support\Facades\Route;
  $currentRouteName = Route::currentRouteName() ?? '';
  $golUrl = $siteSettings['gol_url'] ?? 'https://gol.kpk.go.id';
@endphp

@section('vendor-script')
  @vite(['resources/assets/vendor/js/dropdown-hover.js', 'resources/assets/vendor/js/mega-dropdown.js'])
@endsection

<!-- Navbar: Start -->
<nav class="layout-navbar shadow-none py-0">
  <div class="container">
    <div class="navbar navbar-expand-lg landing-navbar px-3 px-md-6">
      <!-- Menu logo wrapper: Start -->
      <div class="navbar-brand app-brand demo d-flex py-0 me-4 me-xl-6 ms-0">
        <button class="navbar-toggler border-0 px-0 me-3" type="button" data-bs-toggle="collapse"
          data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"
          aria-label="Toggle navigation">
          <i class="icon-base ti tabler-menu-2 icon-lg align-middle text-heading fw-medium"></i>
        </button>
        <a href="{{ route('home') }}" class="app-brand-link">
          <span class="app-brand-logo demo">@include('_partials.macros', ['height' => '34'])</span>
          <span class="app-brand-text demo menu-text fw-bold ms-2 ps-1 text-nowrap">{{ $siteSettings['site_name'] ?? 'UPG KKP' }}</span>
        </a>
      </div>
      <!-- Menu logo wrapper: End -->

      <!-- Menu wrapper: Start -->
      <div class="collapse navbar-collapse landing-nav-menu" id="navbarSupportedContent">
        <button class="navbar-toggler border-0 text-heading position-absolute end-0 top-0 p-2"
          type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
          aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <i class="icon-base ti tabler-x icon-lg"></i>
        </button>
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link fw-medium {{ $currentRouteName === 'home' ? 'active text-primary' : '' }}"
              href="{{ route('home') }}">Beranda</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle fw-medium {{ str_starts_with($currentRouteName, 'profil.') || str_starts_with($currentRouteName, 'contact.') ? 'active text-primary' : '' }}"
              href="javascript:void(0);" data-bs-toggle="dropdown">Profil</a>
            <ul class="dropdown-menu">
              <li>
                <a class="dropdown-item" href="{{ route('profil.tentang') }}">
                  <i class="icon-base ti tabler-info-circle me-2"></i>Tentang UPG
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="{{ route('contact.index') }}">
                  <i class="icon-base ti tabler-mail me-2"></i>Hubungi &amp; Konsultasi UPG
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle fw-medium {{ str_starts_with($currentRouteName, 'publikasi.') || $currentRouteName === 'articles.show' ? 'active text-primary' : '' }}"
              href="javascript:void(0);" data-bs-toggle="dropdown">Publikasi</a>
            <ul class="dropdown-menu">
              <li>
                <a class="dropdown-item" href="{{ route('publikasi.informasi') }}">
                  <i class="icon-base ti tabler-news me-2"></i>Informasi &amp; Berita
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="{{ route('publikasi.infografis') }}">
                  <i class="icon-base ti tabler-photo me-2"></i>Infografis
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="{{ route('publikasi.mengenal') }}">
                  <i class="icon-base ti tabler-book me-2"></i>Mengenal Gratifikasi
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="{{ route('publikasi.videos') }}">
                  <i class="icon-base ti tabler-video me-2"></i>Video Gratifikasi
                </a>
              </li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-medium {{ str_starts_with($currentRouteName, 'downloads.') ? 'active text-primary' : '' }}"
              href="{{ route('downloads.index') }}">Unduh</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle fw-medium {{ str_starts_with($currentRouteName, 'faq.') ? 'active text-primary' : '' }}"
              href="javascript:void(0);" data-bs-toggle="dropdown">FAQ's</a>
            <ul class="dropdown-menu">
              <li>
                <a class="dropdown-item" href="{{ route('faq.index') }}">
                  <i class="icon-base ti tabler-help-circle me-2"></i>FAQ's
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="{{ route('faq.cases') }}">
                  <i class="icon-base ti tabler-scale me-2"></i>Contoh Kasus
                </a>
              </li>
            </ul>
          </li>
        </ul>
      </div>
      <div class="landing-menu-overlay d-lg-none"></div>
      <!-- Menu wrapper: End -->

      <!-- Toolbar: Start -->
      <ul class="navbar-nav flex-row align-items-center ms-auto gap-2">
        <li class="nav-item d-none d-md-block">
          <form action="{{ route('search') }}" method="GET" class="d-flex align-items-center">
            <div class="input-group input-group-sm">
              <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari info/dokumen..." style="max-width: 170px;" />
              <button class="btn btn-outline-secondary" type="submit" title="Cari">
                <i class="icon-base ti tabler-search"></i>
              </button>
            </div>
          </form>
        </li>

        <li class="nav-item dropdown-style-switcher dropdown">
          <a class="nav-link dropdown-toggle hide-arrow" id="nav-theme" href="javascript:void(0);" data-bs-toggle="dropdown">
            <i class="icon-base ti tabler-sun icon-lg theme-icon-active"></i>
            <span class="d-none ms-2" id="nav-theme-text">Toggle theme</span>
          </a>
          <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="nav-theme-text">
            <li>
              <button type="button" class="dropdown-item align-items-center active" data-bs-theme-value="light">
                <span><i class="icon-base ti tabler-sun icon-md me-3" data-icon="sun"></i>Light</span>
              </button>
            </li>
            <li>
              <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="dark">
                <span><i class="icon-base ti tabler-moon-stars icon-md me-3" data-icon="moon-stars"></i>Dark</span>
              </button>
            </li>
            <li>
              <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="system">
                <span><i class="icon-base ti tabler-device-desktop-analytics icon-md me-3" data-icon="device-desktop-analytics"></i>System</span>
              </button>
            </li>
          </ul>
        </li>

        <li>
          <a href="{{ $golUrl }}" class="btn btn-primary btn-sm px-3" target="_blank" rel="noopener noreferrer">
            <i class="icon-base ti tabler-shield-check me-1"></i>
            <span class="d-none d-sm-inline">Lapor GOL</span>
          </a>
        </li>
        <li>
          <a href="{{ auth()->check() ? route('admin.dashboard') : route('admin.login') }}" class="btn btn-label-secondary btn-sm" title="Panel Admin">
            <i class="icon-base ti tabler-user-shield"></i>
          </a>
        </li>
      </ul>
      <!-- Toolbar: End -->
    </div>
  </div>
</nav>
<!-- Navbar: End -->
