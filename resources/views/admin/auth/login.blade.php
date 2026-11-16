@php
  $pageConfigs = ['myLayout' => 'blank'];
  $customizerHidden = 'customizer-hide';
@endphp

@extends('layouts/layoutMaster')

@section('title', 'Login Admin UPG KKP')

@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/page-auth.scss'])
@endsection

@section('content')
<div class="container-xxl">
  <div class="authentication-wrapper authentication-basic container-p-y">
    <div class="authentication-inner py-6">
      <div class="card">
        <div class="card-body">
          <div class="app-brand justify-content-center mb-6">
            <a href="{{ route('home') }}" class="app-brand-link">
              <span class="app-brand-logo demo">@include('_partials.macros', ['height' => '36'])</span>
              <span class="app-brand-text demo text-heading fw-bold">UPG KKP</span>
            </a>
          </div>
          <h4 class="mb-1">Portal Pengelola UPG KKP</h4>
          <p class="mb-6 text-body-secondary">Masuk dengan akun administrator untuk mengelola konten portal.</p>

          @if($errors->any())
            <div class="alert alert-danger py-2">{{ $errors->first() }}</div>
          @endif

          <form class="mb-4" action="{{ route('admin.login.submit') }}" method="POST">
            @csrf
            <div class="mb-4">
              <label for="login" class="form-label">Username atau Email</label>
              <input type="text" class="form-control" id="login" name="login" value="{{ old('login') }}"
                placeholder="adminupg atau upg@kkp.go.id" autofocus required />
            </div>
            <div class="mb-4 form-password-toggle">
              <label class="form-label" for="password">Password</label>
              <div class="input-group input-group-merge">
                <input type="password" id="password" class="form-control" name="password" placeholder="············" required />
                <span class="input-group-text cursor-pointer"><i class="icon-base ti tabler-eye-off"></i></span>
              </div>
            </div>
            <div class="mb-6">
              <div class="form-check">
                <input class="form-check-input" type="checkbox" id="remember" name="remember" />
                <label class="form-check-label" for="remember">Ingat Saya</label>
              </div>
            </div>
            <button class="btn btn-primary d-grid w-100" type="submit">Masuk ke Dashboard</button>
          </form>

          <div class="text-center">
            <a href="{{ route('home') }}" class="small">
              <i class="icon-base ti tabler-arrow-left me-1"></i>Kembali ke Portal Publik
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection

