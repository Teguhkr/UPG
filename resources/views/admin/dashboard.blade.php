@extends('layouts/layoutMaster')

@section('title', 'Dashboard Admin')

@section('vendor-style')
  @vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection

@section('vendor-script')
  @vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('content')
<div class="row g-6 mb-6">
  <div class="col-sm-6 col-xl-3">
    <div class="card h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <span class="text-body-secondary small">Total Artikel</span>
          <h4 class="mb-0 mt-1 fw-bold">{{ number_format($stats['articles']) }}</h4>
          <small class="text-success">{{ number_format($stats['total_article_hits']) }} total dibaca</small>
        </div>
        <div class="avatar avatar-md">
          <span class="avatar-initial rounded bg-label-primary"><i class="icon-base ti tabler-news icon-md"></i></span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <span class="text-body-secondary small">Dokumen Unduh</span>
          <h4 class="mb-0 mt-1 fw-bold">{{ number_format($stats['downloads']) }}</h4>
          <small class="text-primary">{{ number_format($stats['total_download_hits']) }} kali diunduh</small>
        </div>
        <div class="avatar avatar-md">
          <span class="avatar-initial rounded bg-label-info"><i class="icon-base ti tabler-download icon-md"></i></span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <span class="text-body-secondary small">FAQ &amp; Contoh Kasus</span>
          <h4 class="mb-0 mt-1 fw-bold">{{ number_format($stats['faqs']) }}</h4>
          <small class="text-body-secondary">{{ number_format($stats['media']) }} item media</small>
        </div>
        <div class="avatar avatar-md">
          <span class="avatar-initial rounded bg-label-warning"><i class="icon-base ti tabler-help-circle icon-md"></i></span>
        </div>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-xl-3">
    <div class="card h-100">
      <div class="card-body d-flex justify-content-between align-items-center">
        <div>
          <span class="text-body-secondary small">Konsultasi Baru</span>
          <h4 class="mb-0 mt-1 fw-bold">{{ number_format($stats['new_inquiries']) }}</h4>
          <small class="text-danger">Perlu ditindaklanjuti</small>
        </div>
        <div class="avatar avatar-md">
          <span class="avatar-initial rounded bg-label-danger"><i class="icon-base ti tabler-mail icon-md"></i></span>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-6">
  <div class="col-lg-7">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Distribusi Artikel per Kategori</h5>
        <a href="{{ route('admin.articles.create') }}" class="btn btn-sm btn-primary">+ Tambah Artikel</a>
      </div>
      <div class="card-body">
        <div id="categoryArticlesChart"></div>
      </div>
    </div>
  </div>

  <div class="col-lg-5">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Dokumen Paling Banyak Diunduh</h5>
        <a href="{{ route('admin.downloads.index') }}" class="small">Kelola</a>
      </div>
      <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
          <tbody>
            @foreach($topDownloads as $dl)
              <tr>
                <td class="ps-4">
                  <div class="fw-medium small text-truncate" style="max-width: 240px;">{{ $dl->title }}</div>
                  <small class="text-body-secondary">{{ $dl->category->name ?? '' }}</small>
                </td>
                <td class="text-end pe-4">
                  <span class="badge bg-label-primary">{{ number_format($dl->download_count) }}x</span>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Artikel Terbaru Diperbarui</h5>
        <a href="{{ route('admin.articles.index') }}" class="small">Semua Artikel</a>
      </div>
      <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
          <tbody>
            @foreach($recentArticles as $art)
              <tr>
                <td class="ps-4">
                  <a href="{{ route('admin.articles.edit', $art) }}" class="fw-medium text-heading">{{ \Illuminate\Support\Str::limit($art->title, 48) }}</a>
                  <small class="d-block text-body-secondary">{{ $art->category->name ?? '' }}</small>
                </td>
                <td class="text-end pe-4">
                  <span class="badge {{ $art->is_published ? 'bg-label-success' : 'bg-label-secondary' }}">
                    {{ $art->is_published ? 'Terbit' : 'Draft' }}
                  </span>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <div class="col-lg-6">
    <div class="card h-100">
      <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Konsultasi Masuk Terbaru</h5>
        <a href="{{ route('admin.inquiries.index') }}" class="small">Semua Pesan</a>
      </div>
      <div class="table-responsive">
        <table class="table table-sm align-middle mb-0">
          <tbody>
            @forelse($recentInquiries as $inq)
              <tr>
                <td class="ps-4">
                  <div class="fw-medium">{{ $inq->subject }}</div>
                  <small class="text-body-secondary">{{ $inq->name }} ({{ $inq->email }})</small>
                </td>
                <td class="text-end pe-4">
                  <span class="badge bg-label-{{ $inq->status === 'selesai' ? 'success' : ($inq->status === 'diproses' ? 'warning' : 'danger') }}">
                    {{ ucfirst($inq->status) }}
                  </span>
                </td>
              </tr>
            @empty
              <tr><td class="text-center py-4 text-body-secondary">Belum ada pesan konsultasi masuk.</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const el = document.querySelector('#categoryArticlesChart');
  if (el && typeof ApexCharts !== 'undefined') {
    const data = @json($categoryChart);
    new ApexCharts(el, {
      chart: { type: 'bar', height: 290, toolbar: { show: false } },
      series: [{ name: 'Jumlah Artikel', data: data.map(i => i.count) }],
      xaxis: { categories: data.map(i => i.name) },
      colors: ['#d92b2b'],
      plotOptions: { bar: { borderRadius: 6, columnWidth: '45%' } },
      dataLabels: { enabled: true }
    }).render();
  }
});
</script>
@endsection

