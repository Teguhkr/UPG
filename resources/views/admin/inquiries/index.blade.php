@extends('layouts/layoutMaster')

@section('title', 'Konsultasi & Pesan Masuk')

@section('content')
@if(session('success'))
  <div class="alert alert-success alert-dismissible">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<div class="card">
  <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-3">
    <h5 class="mb-0">Pesan Konsultasi Gratifikasi</h5>
    <div class="d-flex gap-2">
      <a href="{{ route('admin.inquiries.index') }}" class="btn btn-sm {{ !$status ? 'btn-primary' : 'btn-label-primary' }}">Semua</a>
      <a href="{{ route('admin.inquiries.index', ['status' => 'baru']) }}" class="btn btn-sm {{ $status === 'baru' ? 'btn-danger' : 'btn-label-danger' }}">Baru</a>
      <a href="{{ route('admin.inquiries.index', ['status' => 'diproses']) }}" class="btn btn-sm {{ $status === 'diproses' ? 'btn-warning' : 'btn-label-warning' }}">Diproses</a>
      <a href="{{ route('admin.inquiries.index', ['status' => 'selesai']) }}" class="btn btn-sm {{ $status === 'selesai' ? 'btn-success' : 'btn-label-success' }}">Selesai</a>
    </div>
  </div>
  <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
      <thead class="table-light">
        <tr>
          <th>Pengirim</th>
          <th>Subjek</th>
          <th>Status</th>
          <th>Tanggal</th>
          <th class="text-end">Aksi</th>
        </tr>
      </thead>
      <tbody>
        @forelse($inquiries as $inq)
          <tr>
            <td>
              <div class="fw-semibold">{{ $inq->name }}</div>
              <small class="text-body-secondary">{{ $inq->email }} {{ $inq->unit_kerja ? '• ' . $inq->unit_kerja : '' }}</small>
            </td>
            <td>{{ $inq->subject }}</td>
            <td>
              <span class="badge bg-label-{{ $inq->status === 'selesai' ? 'success' : ($inq->status === 'diproses' ? 'warning' : 'danger') }}">
                {{ ucfirst($inq->status) }}
              </span>
            </td>
            <td><small>{{ $inq->created_at->format('d/m/Y H:i') }}</small></td>
            <td class="text-end text-nowrap">
              <button type="button" class="btn btn-sm btn-label-primary" data-bs-toggle="modal" data-bs-target="#inqModal{{ $inq->id }}">
                Detail / Tindak Lanjut
              </button>
              <form action="{{ route('admin.inquiries.destroy', $inq) }}" method="POST" class="d-inline" onsubmit="return confirm('Hapus pesan ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-sm btn-icon btn-label-danger"><i class="icon-base ti tabler-trash"></i></button>
              </form>
            </td>
          </tr>

          <div class="modal fade" id="inqModal{{ $inq->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
              <form method="POST" action="{{ route('admin.inquiries.update', $inq) }}" class="modal-content">
                @csrf @method('PUT')
                <div class="modal-header">
                  <h5 class="modal-title">{{ $inq->subject }}</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                  <p class="mb-2"><strong>Dari:</strong> {{ $inq->name }} ({{ $inq->email }}) | <strong>Telp:</strong> {{ $inq->phone ?: '-' }}</p>
                  <p class="mb-3"><strong>Unit Kerja:</strong> {{ $inq->unit_kerja ?: '-' }}</p>
                  <div class="p-3 bg-body-secondary rounded mb-4">{!! nl2br(e($inq->message)) !!}</div>
                  <div class="mb-3">
                    <label class="form-label">Status Penanganan</label>
                    <select name="status" class="form-select">
                      <option value="baru" {{ $inq->status === 'baru' ? 'selected' : '' }}>Baru</option>
                      <option value="diproses" {{ $inq->status === 'diproses' ? 'selected' : '' }}>Sedang Diproses</option>
                      <option value="selesai" {{ $inq->status === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    </select>
                  </div>
                  <div>
                    <label class="form-label">Catatan Tindak Lanjut Admin</label>
                    <textarea name="admin_notes" rows="3" class="form-control">{{ $inq->admin_notes }}</textarea>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="submit" class="btn btn-primary">Simpan Status</button>
                </div>
              </form>
            </div>
          </div>
        @empty
          <tr><td colspan="5" class="text-center py-6 text-body-secondary">Belum ada pesan konsultasi masuk.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
  @if($inquiries->hasPages())
    <div class="card-footer">{{ $inquiries->links() }}</div>
  @endif
</div>
@endsection

