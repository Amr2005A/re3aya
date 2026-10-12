<!doctype html>
<html lang="ar" dir="rtl"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>طلب حساب | رعاية</title>
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
<link href="{{ asset('css/riaya.css') }}" rel="stylesheet"></head>
<body class="d-flex align-items-center justify-content-center min-vh-100 p-3" style="background:linear-gradient(160deg,var(--r-dark),#3a5557)">
<div class="card w-100" style="max-width:440px"><div class="card-body p-4">
  <h1 class="h3 fw-bold text-center mb-1" style="color:var(--r-dark)">رِعاية</h1>
  <p class="text-center text-muted mb-4">طلب حساب جديد</p>
  <div class="alert alert-info py-2 small">يُفعَّل الحساب بعد مراجعة الإدارة، ولا يملك أي صلاحية حتى تمنحه إياها.</div>
  <form method="POST" action="{{ route('register') }}">@csrf
    <div style="position:absolute;left:-9999px" aria-hidden="true"><input type="text" name="website" tabindex="-1" autocomplete="off"></div>
    @foreach([['name','الاسم','text',true],['email','البريد الإلكتروني','email',true],['username','اسم المستخدم (اختياري)','text',false]] as [$f,$l,$t,$req])
      <div class="mb-3"><label class="form-label">{{ $l }}</label>
        <input type="{{ $t }}" name="{{ $f }}" value="{{ old($f) }}" class="form-control @error($f) is-invalid @enderror" @required($req)>
        @error($f)<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    @endforeach
    <div class="mb-3"><label class="form-label">كلمة المرور</label><input type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" required>@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="mb-3"><label class="form-label">تأكيد كلمة المرور</label><input type="password" name="password_confirmation" class="form-control" autocomplete="new-password" required></div>
    <button class="btn btn-primary w-100 py-2">إرسال الطلب</button>
  </form>
  <div class="text-center mt-3 small"><a href="{{ route('login') }}">لديّ حساب بالفعل</a></div>
</div></div></body></html>
