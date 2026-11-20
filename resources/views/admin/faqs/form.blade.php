@extends('layouts/layoutMaster')

@section('title', $faq->exists ? 'Edit FAQ / Kasus' : 'Tambah FAQ / Kasus')

@section('content')
<div class="card">
  <div class="card-header d-flex justify-content-between align-items-center">
    <h5 class="mb-0">{{ $faq->exists ? 'Edit FAQ / Contoh Kasus' : 'Tambah FAQ / Contoh Kasus' }}</h5>
    <a href="{{ route('admin.faqs.index') }}" class="btn btn-sm btn-label-secondary">Kembali</a>
  </div>
  <div class="card-body">
    <form method="POST" action="{{ $faq->exists ? route('admin.faqs.update', $faq) : route('admin.faqs.store') }}">
      @csrf
      @if($faq->exists) @method('PUT') @endif

      <div class="row g-4">
        <div class="col-md-4">
          <label class="form-label">Jenis <span class="text-danger">*</span></label>
          <select name="category_type" class="form-select" required>
            <option value="faq" {{ old('category_type', $faq->category_type) === 'faq' ? 'selected' : '' }}>FAQ's</option>
            <option value="contoh_kasus" {{ old('category_type', $faq->category_type) === 'contoh_kasus' ? 'selected' : '' }}>Contoh Kasus</option>
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label">Urutan</label>
          <input type="number" name="sort_order" min="1" value="{{ old('sort_order', $faq->sort_order) }}" class="form-control" />
        </div>
        <div class="col-md-4 d-flex align-items-end pb-2">
          <div class="form-check">
            <input type="checkbox" class="form-check-input" id="faq_pub" name="is_published" value="1"
              {{ old('is_published', $faq->is_published) ? 'checked' : '' }} />
            <label class="form-check-label" for="faq_pub">Terbitkan</label>
          </div>
        </div>
        <div class="col-12">
          <label class="form-label">Pertanyaan / Judul Kasus <span class="text-danger">*</span></label>
          <input type="text" name="question" value="{{ old('question', $faq->question) }}" class="form-control" required />
        </div>
        <div class="col-12">
          <label class="form-label">Jawaban / Penjelasan (HTML) <span class="text-danger">*</span></label>
          <textarea name="answer" rows="10" class="form-control font-monospace" required>{{ old('answer', $faq->answer) }}</textarea>
        </div>
        <div class="col-12">
          <button type="submit" class="btn btn-primary">
            <i class="icon-base ti tabler-device-floppy me-1"></i> Simpan
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
@endsection

