<!doctype html>
<html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>تسجيل الدخول | رعاية</title>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<link href="{{ asset('css/riaya.css') }}" rel="stylesheet"></head>
<body class="d-flex align-items-center justify-content-center min-vh-100 p-3" style="background:linear-gradient(160deg,var(--r-dark),#3a5557)">
<div class="card w-100" style="max-width:400px"><div class="card-body p-4">
  <h1 class="h3 fw-bold text-center mb-1" style="color:var(--r-dark)">رِعاية</h1>
  <p class="text-center text-muted mb-4">تسجيل الدخول إلى لوحة الإدارة</p>
  <form method="POST" action="{{ route('login') }}">@csrf
    <div class="mb-3"><label class="form-label">البريد أو اسم المستخدم</label>
      <input name="login" value="{{ old('login') }}" class="form-control @error('login') is-invalid @enderror" autofocus required>
      @error('login')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="mb-3"><label class="form-label">كلمة المرور</label><input type="password" name="password" class="form-control" required></div>
    <div class="form-check mb-3"><input type="checkbox" class="form-check-input" name="remember" id="rm"><label class="form-check-label" for="rm">تذكّرني</label></div>
    <button class="btn btn-primary w-100 py-2">دخول</button>
  </form>
</div></div></body></html>
