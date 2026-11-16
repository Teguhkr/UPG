@php
$containerFooter = ($configData['contentLayout'] === 'compact') ? 'container-xxl' : 'container-fluid';
@endphp

<!-- Footer-->
<footer class="content-footer footer bg-footer-theme">
  <div class="{{ $containerFooter }}">
    <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
      <div class="text-body">
        © {{ date('Y') }}, <strong>Unit Pengendalian Gratifikasi (UPG) KKP RI</strong>
      </div>
      <div class="d-none d-lg-inline-block">
        <a href="{{ route('home') }}" target="_blank" class="footer-link me-4">Portal Publik</a>
        <a href="{{ $siteSettings['gol_url'] ?? 'https://gol.kpk.go.id' }}" target="_blank" class="footer-link">GOL KPK</a>
      </div>
    </div>
  </div>
</footer>
<!--/ Footer-->
