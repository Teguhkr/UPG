@php
  use Illuminate\Support\Facades\Auth;
@endphp

@if (isset($navbarFull))
  <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 me-4 ms-0">
    <a href="{{ route('admin.dashboard') }}" class="app-brand-link">
      <span class="app-brand-logo demo">@include('_partials.macros')</span>
      <span class="app-brand-text demo menu-text fw-bold">UPG KKP</span>
    </a>
  </div>
@endif

@if (!isset($navbarHideToggle))
  <div class="layout-menu-toggle navbar-nav align-items-xl-center me-4 me-xl-0{{ isset($menuHorizontal) ? ' d-xl-none ' : '' }} {{ isset($contentNavbar) ? ' d-xl-none ' : '' }}">
    <a class="nav-item nav-link px-0 me-xl-6" href="javascript:void(0)">
      <i class="icon-base ti tabler-menu-2 icon-md"></i>
    </a>
  </div>
@endif

<div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
  <div class="navbar-nav align-items-center">
    <a href="{{ route('home') }}" target="_blank" class="btn btn-sm btn-label-primary">
      <i class="icon-base ti tabler-external-link me-1"></i> Portal Publik
    </a>
  </div>

  <ul class="navbar-nav flex-row align-items-center ms-auto">
    <!-- Theme Switcher -->
    <li class="nav-item dropdown me-2">
      <a class="nav-link dropdown-toggle hide-arrow" id="nav-theme" href="javascript:void(0);" data-bs-toggle="dropdown">
        <i class="icon-base ti tabler-sun icon-md theme-icon-active"></i>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <button type="button" class="dropdown-item align-items-center active" data-bs-theme-value="light">
            <span><i class="icon-base ti tabler-sun icon-22px me-3" data-icon="sun"></i>Light</span>
          </button>
        </li>
        <li>
          <button type="button" class="dropdown-item align-items-center" data-bs-theme-value="dark">
            <span><i class="icon-base ti tabler-moon-stars icon-22px me-3" data-icon="moon-stars"></i>Dark</span>
          </button>
        </li>
      </ul>
    </li>

    <!-- User -->
    <li class="nav-item navbar-dropdown dropdown-user dropdown">
      <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
        <div class="avatar avatar-online">
          <span class="avatar-initial rounded-circle bg-label-primary fw-bold">
            {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
          </span>
        </div>
      </a>
      <ul class="dropdown-menu dropdown-menu-end">
        <li>
          <div class="dropdown-item mt-0">
            <div class="d-flex align-items-center">
              <div class="flex-shrink-0 me-2">
                <div class="avatar avatar-online">
                  <span class="avatar-initial rounded-circle bg-label-primary fw-bold">
                    {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                  </span>
                </div>
              </div>
              <div class="flex-grow-1">
                <h6 class="mb-0">{{ Auth::user()->name ?? 'Admin' }}</h6>
                <small class="text-body-secondary text-uppercase">{{ str_replace('_', ' ', Auth::user()->role ?? 'admin') }}</small>
              </div>
            </div>
          </div>
        </li>
        <li><div class="dropdown-divider my-1 mx-n2"></div></li>
        <li>
          <a class="dropdown-item" href="{{ route('admin.settings.index') }}">
            <i class="icon-base ti tabler-settings me-3 icon-md"></i><span>Pengaturan Situs</span>
          </a>
        </li>
        <li>
          <form method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit" class="dropdown-item text-danger">
              <i class="icon-base ti tabler-logout me-3 icon-md"></i><span>Keluar</span>
            </button>
          </form>
        </li>
      </ul>
    </li>
  </ul>
</div>
