@extends('layouts/layoutMaster')

@section('title', 'Kelola FAQ & Contoh Kasus')

@section('content')
@if(session('success'))
  <div class="alert alert-success alert-dismissible">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card">
  <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <h5 class="mb-0">FAQ &amp; Contoh Kasus Gratifikasi</h5>
    <a href="{{ route('admin.faqs.create') }}" class="btn btn-primary">
      <i class="icon-base ti tabler-plus me-1"></i> Tambah Item
    </a>
  </div>
  <div class="card-body border-bottom">
    <form method="GET" class="row g-3">
      <div class="col-md-4">
        <select name="type" class="form-select" onchange="this.form.submit()">
          <option value="">-- Semua Jenis --</option>
          <option value="faq" {{ $type === 'faq' ? 'selected' : '' }}>FAQ's</option>
          <option value="contoh_kasus" {{ $type === 'contoh_kasus' ? 'selected' : '' }}>Contoh Kasus</option>
        </select>
      </div>
      <div class="col-md-5">
        <input type="text" name="q" value="{{ $search }}" class="form-control" placeholder="Cari pertanyaan..." />
      </div>
      <div class="col-md-3 d-flex gap-2">
        <button class="btn btn-label-primary" type="submit">Filter</button>
        <a href="{{ route('admin.faqs.index') }}" class="btn btn-label-secondary">Reset</a>
      </div>
    </form>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Urutan</th>
          <th>Jenis</th>
          <th>Pertanyaan / Kasus</th>
          <th>Status</th>
          <th class="text-end">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($faqs as $faq)
          <tr>
            <td>{{ $faq->sort_order }}</td>
            <td>
              <span class="badge bg-label-{{ $faq->category_type === 'contoh_kasus' ? 'warning' : 'primary' }}">
                {{ $faq->category_type === 'contoh_kasus' ? 'Contoh Kasus' : 'FAQ' }}
              </span>
            </td>
            <td class="fw-medium">{{ $faq->question }}</td>
            <td>
              <span class="badge bg-label-{{ $faq->is_published ? 'success' : 'secondary' }}">
                {{ $faq->is_published ? 'Terbit' : 'Draft' }}
              </span>
            </td>
            <td class="text-end text-nowrap">
              <a href="{{ route('admin.faqs.edit', $faq) }}" class="btn btn-sm btn-icon btn-label-primary"><i class="icon-base ti tabler-edit"></i></a>
              <form action="{{ route('admin.faqs.destroy', $faq) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus item ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-icon btn-label-danger"><i class="icon-base ti tabler-trash"></i></button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="text-center py-6 text-body-secondary">Belum ada data.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($faqs->hasPages())
    <div class="card-footer">{{ $faqs->links() }}</div>
  @endif
</div>
@endsection

