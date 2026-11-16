@extends('layouts/layoutMaster')

@section('title', 'Pengaturan Situs')

@section('content')
@if(session('success'))
  <div class="alert alert-success alert-dismissible">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

<form method="POST" action="{{ route('admin.settings.update') }}">
  @csrf @method('PUT')
  <div class="row g-6">
    @foreach($settings as $group => $items)
      <div class="col-lg-6">
        <div class="card h-100">
          <div class="card-header">
            <h5 class="mb-0 text-capitalize">Pengaturan {{ $group }}</h5>
          </div>
          <div class="card-body">
            @foreach($items as $setting)
              <div class="mb-4">
                <label class="form-label">{{ $setting->label }}</label>
                @if($setting->type === 'textarea')
                  <textarea name="settings[{{ $setting->key }}]" rows="3" class="form-control">{{ $setting->value }}</textarea>
                @else
                  <input type="{{ $setting->type === 'url' ? 'url' : 'text' }}"
                    name="settings[{{ $setting->key }}]" value="{{ $setting->value }}" class="form-control" />
                @endif
              </div>
            @endforeach
          </div>
        </div>
      </div>
    @endforeach
    <div class="col-12">
      <button type="submit" class="btn btn-primary btn-lg">
        <i class="icon-base ti tabler-device-floppy me-1"></i> Simpan Semua Pengaturan
      </button>
    </div>
  </div>
</form>
@endsection

