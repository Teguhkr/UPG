@php
  $pageConfigs = ['myLayout' => 'front'];
  $golUrl = $siteSettings['gol_url'] ?? 'https://gol.kpk.go.id';
  use Illuminate\Support\Facades\Route;
  $currentRouteName = Route::currentRouteName() ?? '';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Beranda')

@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/front-page-landing.scss'])
@endsection

@section('content')
<div data-bs-spy="scroll" class="scrollspy-example">
  <!-- Hero Section -->
  <section id="landingHero" class="section-py landing-hero position-relative">
    <div class="container position-relative">
      <div class="row align-items-center gy-6 py-4">
        <div class="col-lg-7 text-center text-lg-start">
          @php
            $heroTitle = $siteSettings['hero_title'] ?? 'Wujudkan Integritas Kementerian Kelautan dan Perikanan Bebas Gratifikasi';
            $heroTitleHighlight = 'Kelautan dan Perikanan Bebas Gratifikasi';
          @endphp
          <span class="badge bg-label-primary mb-3 px-3 py-2 hero-tagline">
            <i class="icon-base ti tabler-shield-check me-1"></i>
            {{ $siteSettings['site_tagline'] ?? 'Unit Pengendalian Gratifikasi KKP RI' }}
          </span>
          <h1 class="text-black hero-title display-6 fw-extrabold mb-4">
            @if(str_contains($heroTitle, $heroTitleHighlight))
              {{ str_replace($heroTitleHighlight, '', $heroTitle) }}<span class="hero-title-highlight">{{ $heroTitleHighlight }}</span>
            @else
              {{ $heroTitle }}
            @endif
          </h1>
          <p class="hero-sub-title h6 fw-normal text-body mb-6">
            {{ $siteSettings['hero_subtitle'] ?? 'Portal resmi Unit Pengendalian Gratifikasi (UPG) KKP sebagai pusat informasi, edukasi, konsultasi, dan pelaporan gratifikasi.' }}
          </p>
          <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3">
            <a href="{{ route('contact.index') }}" target="_blank" rel="noopener noreferrer"
              class="btn btn-primary btn-lg upg-gol-pulse shadow-sm">
              <i class="icon-base ti tabler-send me-2"></i>Konsultasi Gratifikasi
            </a>
            <a href="{{ route('publikasi.mengenal') }}" class="btn btn-label-primary btn-lg">
              <i class="icon-base ti tabler-book me-2"></i>Mengenal Gratifikasi
            </a>
            <a href="{{ route('downloads.index') }}" class="btn btn-outline-secondary btn-lg">
              <i class="icon-base ti tabler-download me-2"></i>Pusat Unduhan
            </a>
          </div>
        </div>
        <div class="col-lg-5 text-center">
          <div class="card border-0 shadow-lg bg-body">
            <div class="card-body p-6 text-center">
              <a href="{{ $golUrl }}" target="_blank" rel="noopener noreferrer" class="text-decoration-none">
                <img src="{{ asset('storage/upg-media/tombol/gol-logo.png') }}" alt="GOL KPK"
                  class="img-fluid mb-3 upg-gol-pulse" style="max-height: 110px;" />
                <h4 class="text-danger fw-bold mb-1">Lapor Gratifikasi Online</h4>
                <p class="text-body-secondary small mb-3">Klik di sini untuk melaporkan penerimaan atau penolakan gratifikasi melalui aplikasi resmi GOL KPK RI.</p>
                <span class="btn btn-danger w-100">
                  Buka Aplikasi GOL KPK <i class="icon-base ti tabler-external-link ms-1"></i>
                </span>
              </a>
              <hr class="my-4" />
              <div class="row g-3 text-center">
                <div class="col-4">
                  <div class="fw-bold h5 mb-0 text-primary">{{ number_format($stats['articles']) }}</div>
                  <small class="text-body-secondary">Artikel</small>
                </div>
                <div class="col-4">
                  <div class="fw-bold h5 mb-0 text-primary">{{ number_format($stats['downloads']) }}</div>
                  <small class="text-body-secondary">Dokumen</small>
                </div>
                <div class="col-4">
                  <div class="fw-bold h5 mb-0 text-primary">{{ number_format($stats['total_download_hits']) }}</div>
                  <small class="text-body-secondary">Unduhan</small>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Mengenal Gratifikasi Quick Pillars -->
  <section class="section-py bg-body">
    <div class="container">
      <div class="text-center mb-8">
        <span class="badge bg-label-primary mb-2">Edukasi Integritas</span>
        <h3 class="mb-2">Mengenal Pengendalian Gratifikasi</h3>
        <p class="text-body-secondary mb-0">Pahami dasar hukum, mekanisme pelaporan, serta daftar gratifikasi yang wajib dan tidak wajib dilaporkan.</p>
      </div>
      <div class="row g-6">
        @foreach($mengenalArticles as $item)
          <div class="col-md-6 col-lg-3">
            <div class="card h-100 shadow-sm border">
              <div class="card-body d-flex flex-column">
                <div class="avatar avatar-md mb-4">
                  <span class="avatar-initial rounded bg-label-primary">
                    <i class="icon-base ti tabler-scale icon-md"></i>
                  </span>
                </div>
                <h5 class="card-title mb-2">{{ $item->title }}</h5>
                <p class="card-text text-body-secondary small grow">{{ \Illuminate\Support\Str::limit($item->excerpt, 110) }}</p>
                <a href="{{ route('articles.show', $item->slug) }}" class="btn btn-sm btn-label-primary mt-3 align-self-start">
                  Baca Selengkapnya <i class="icon-base ti tabler-arrow-right ms-1"></i>
                </a>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- Informasi & Berita Terbaru -->
  <section class="section-py bg-body-secondary">
    <div class="container">
      <div class="d-flex flex-wrap justify-content-between align-items-end mb-6 gap-3">
        <div>
          <span class="badge bg-label-primary mb-2">Publikasi Terkini</span>
          <h3 class="mb-1">Informasi &amp; Berita Seputar PPG</h3>
          <p class="text-body-secondary mb-0">Berita kegiatan, surat edaran, dan sosialisasi pengendalian gratifikasi di KKP.</p>
        </div>
        <a href="{{ route('publikasi.informasi') }}" class="btn btn-primary">
          Semua Informasi <i class="icon-base ti tabler-arrow-right ms-1"></i>
        </a>
      </div>

      <div class="row g-6">
        @foreach($latestArticles as $article)
          <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm">
              @if($article->image_url)
                <img src="{{ $article->image_url }}" class="card-img-top object-fit-cover" alt="{{ $article->title }}" style="height: 200px;" />
              @else
                <div class="bg-label-primary d-flex align-items-center justify-content-center" style="height: 200px;">
                  <i class="icon-base ti tabler-news icon-xl"></i>
                </div>
              @endif
              <div class="card-body d-flex flex-column">
                <div class="d-flex align-items-center gap-2 mb-2 text-body-secondary small">
                  <i class="icon-base ti tabler-calendar icon-xs"></i>
                  <span>{{ optional($article->published_at)->translatedFormat('d M Y') }}</span>
                  @if($article->is_featured)
                    <span class="badge bg-label-danger ms-auto">Utama</span>
                  @endif
                </div>
                <h5 class="card-title mb-2">
                  <a href="{{ route('articles.show', $article->slug) }}" class="text-heading text-decoration-none">
                    {{ $article->title }}
                  </a>
                </h5>
                <p class="card-text text-body-secondary small grow">{{ \Illuminate\Support\Str::limit($article->excerpt, 130) }}</p>
                <a href="{{ route('articles.show', $article->slug) }}" class="fw-medium text-primary mt-2 text-decoration-none">
                  Selengkapnya <i class="icon-base ti tabler-chevron-right icon-xs"></i>
                </a>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>

  <!-- FAQ & Contoh Kasus Accordion -->
  <section id="landingFAQ" class="section-py bg-body">
    <div class="container">
      <div class="row g-8">
        <div class="col-lg-6">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
              <span class="badge bg-label-primary mb-2">FAQ's</span>
              <h4 class="mb-0">Hal yang Sering Ditanyakan</h4>
            </div>
            <a href="{{ route('faq.index') }}" class="btn btn-sm btn-label-primary">Lihat Semua</a>
          </div>
          <div class="accordion" id="homeFaqAccordion">
            @foreach($faqs as $idx => $faq)
              <div class="accordion-item">
                <h2 class="accordion-header" id="faqHeading{{ $faq->id }}">
                  <button class="accordion-button {{ $idx > 0 ? 'collapsed' : '' }}" type="button"
                    data-bs-toggle="collapse" data-bs-target="#faqCollapse{{ $faq->id }}"
                    aria-expanded="{{ $idx === 0 ? 'true' : 'false' }}" aria-controls="faqCollapse{{ $faq->id }}">
                    {{ $faq->question }}
                  </button>
                </h2>
                <div id="faqCollapse{{ $faq->id }}" class="accordion-collapse collapse {{ $idx === 0 ? 'show' : '' }}"
                  aria-labelledby="faqHeading{{ $faq->id }}" data-bs-parent="#homeFaqAccordion">
                  <div class="accordion-body upg-prose small">
                    {!! $faq->answer !!}
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        <div class="col-lg-6">
          <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
              <span class="badge bg-label-warning mb-2">Studi Kasus</span>
              <h4 class="mb-0">Contoh Kasus Gratifikasi</h4>
            </div>
            <a href="{{ route('faq.cases') }}" class="btn btn-sm btn-label-warning">Lihat Semua</a>
          </div>
          <div class="accordion mb-6" id="homeCaseAccordion">
            @foreach($contohKasus as $idx => $kasus)
              <div class="accordion-item">
                <h2 class="accordion-header" id="caseHeading{{ $kasus->id }}">
                  <button class="accordion-button {{ $idx > 0 ? 'collapsed' : '' }}" type="button"
                    data-bs-toggle="collapse" data-bs-target="#caseCollapse{{ $kasus->id }}"
                    aria-expanded="{{ $idx === 0 ? 'true' : 'false' }}" aria-controls="caseCollapse{{ $kasus->id }}">
                    {{ $kasus->question }}
                  </button>
                </h2>
                <div id="caseCollapse{{ $kasus->id }}" class="accordion-collapse collapse {{ $idx === 0 ? 'show' : '' }}"
                  aria-labelledby="caseHeading{{ $kasus->id }}" data-bs-parent="#homeCaseAccordion">
                  <div class="accordion-body upg-prose small">
                    {!! $kasus->answer !!}
                  </div>
                </div>
              </div>
            @endforeach
          </div>

          <!-- Popular Downloads Box -->
          <div class="card border shadow-none">
            <div class="card-header d-flex justify-content-between align-items-center py-3">
              <h6 class="mb-0"><i class="icon-base ti tabler-file-download me-2 text-primary"></i>Dokumen Terpopuler</h6>
              <a href="{{ route('downloads.index') }}" class="small">Semua Dokumen</a>
            </div>
            <div class="list-group list-group-flush">
              @foreach($popularDownloads as $dl)
                <a href="{{ route('downloads.file', $dl->slug) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                  <div class="me-3 text-truncate">
                    <span class="badge bg-label-primary me-2">{{ $dl->extension }}</span>
                    <span class="small fw-medium">{{ $dl->title }}</span>
                  </div>
                  <span class="badge bg-label-secondary rounded-pill">{{ number_format($dl->download_count) }}x</span>
                </a>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Infografis & Video Highlight -->
  <section class="section-py bg-body-secondary">
    <div class="container">
      <div class="d-flex flex-wrap justify-content-between align-items-end mb-6 gap-3">
        <div>
          <span class="badge bg-label-primary mb-2">Galeri Edukasi</span>
          <h3 class="mb-1">Infografis &amp; Video Pengendalian Gratifikasi</h3>
        </div>
        <div class="d-flex gap-2">
          <a href="{{ route('publikasi.infografis') }}" class="btn btn-label-primary">Semua Infografis</a>
          <a href="{{ route('publikasi.videos') }}" class="btn btn-primary">Semua Video</a>
        </div>
      </div>

      <div class="row g-5 mb-6">
        @foreach($infografis as $info)
          <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('publikasi.infografis') }}" class="card h-100 shadow-sm text-decoration-none overflow-hidden">
              <img src="{{ $info->file_url }}" alt="{{ $info->title }}" class="card-img-top object-fit-cover" style="height: 180px;" />
              <div class="card-body p-2 text-center">
                <small class="text-heading fw-medium d-block text-truncate">{{ $info->title }}</small>
              </div>
            </a>
          </div>
        @endforeach
      </div>

      <div class="row g-6">
        @foreach($videos as $video)
          <div class="col-md-4">
            <div class="card h-100 shadow-sm">
              <div class="card-body">
                @if($video->file_url)
                  <video class="w-100 rounded mb-3" controls preload="metadata" poster="{{ $video->poster_url }}" style="max-height: 200px; background:#000;">
                    <source src="{{ $video->file_url }}" type="video/mp4" />
                  </video>
                @elseif($video->embed_url)
                  <div class="ratio ratio-16x9 mb-3">
                    <iframe src="{{ $video->embed_url }}" allowfullscreen class="rounded"></iframe>
                  </div>
                @endif
                <h6 class="mb-1">{{ $video->title }}</h6>
                <p class="small text-body-secondary mb-0">{{ $video->description }}</p>
              </div>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </section>
</div>
@endsection

