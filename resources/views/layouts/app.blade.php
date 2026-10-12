<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'رعاية') | رعاية</title>
  <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
  <link href="{{ asset('css/riaya.css') }}" rel="stylesheet">
</head>
<body>
<div class="d-flex">
  @include('partials.sidebar')
  <div class="flex-grow-1 min-vw-0">
    <header class="r-topbar d-flex align-items-center justify-content-between px-3 py-2">
      <button class="btn btn-light d-lg-none" onclick="document.getElementById('sb').classList.toggle('open')" aria-label="القائمة">☰</button>
      <nav aria-label="breadcrumb" class="me-2"><ol class="breadcrumb mb-0">@yield('breadcrumbs')</ol></nav>
      <div class="d-flex align-items-center gap-2 ms-auto">
        <span class="small text-muted d-none d-sm-inline">{{ auth()->user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-sm btn-outline-secondary">خروج</button></form>
      </div>
    </header>
    <main class="p-3 p-lg-4">
      <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
        <h1 class="h4 fw-bold mb-0">@yield('title')</h1>@yield('actions')
      </div>
      @yield('content')
    </main>
  </div>
</div>
@if(session('success') || $errors->any())
<div class="toast-container position-fixed bottom-0 start-0 p-3">
  <div class="toast show text-bg-{{ $errors->any() ? 'danger' : 'success' }}" role="alert">
    <div class="toast-body">{{ session('success') ?? 'يرجى مراجعة الحقول المظللة.' }}</div>
  </div>
</div>
@endif
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>document.querySelectorAll('form[data-confirm]').forEach(f=>f.addEventListener('submit',e=>{if(!confirm(f.dataset.confirm))e.preventDefault()}));
setTimeout(()=>document.querySelectorAll('.toast.show').forEach(t=>t.remove()),4500);</script>
</body></html>
