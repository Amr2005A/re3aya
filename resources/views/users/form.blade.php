@extends('layouts.app')
@section('title', $user->exists ? 'تعديل مستخدم' : 'مستخدم جديد')
@section('breadcrumbs')<li class="breadcrumb-item"><a href="{{ route('users.index') }}">المستخدمون</a></li><li class="breadcrumb-item active">@yield('title')</li>@endsection
@section('content')
<form method="POST" class="card" style="max-width:640px" action="{{ $user->exists ? route('users.update', $user) : route('users.store') }}">
  @csrf @if($user->exists) @method('PUT') @endif
  <div class="card-body row g-3">
    @foreach([['name','الاسم','text'],['email','البريد الإلكتروني','email'],['username','اسم المستخدم (اختياري)','text']] as [$f,$l,$t])
      <div class="col-12"><label class="form-label">{{ $l }}</label>
        <input type="{{ $t }}" name="{{ $f }}" value="{{ old($f, $user->$f) }}" class="form-control @error($f) is-invalid @enderror" @required($f!=='username')>
        @error($f)<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    @endforeach
    <div class="col-md-6"><label class="form-label">كلمة المرور @if($user->exists)<small class="text-muted">(اتركها فارغة للإبقاء)</small>@endif</label>
      <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
      @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-6"><label class="form-label">تأكيد كلمة المرور</label><input type="password" name="password_confirmation" class="form-control"></div>
    <div class="col-12 form-check ms-3"><input type="hidden" name="is_active" value="0">
      <input type="checkbox" class="form-check-input" name="is_active" value="1" id="act" @checked(old('is_active', $user->exists ? $user->is_active : true))>
      <label class="form-check-label" for="act">الحساب مفعّل</label></div>
  </div>
  <div class="card-footer bg-transparent d-flex gap-2"><button class="btn btn-primary">حفظ</button><a class="btn btn-light" href="{{ route('users.index') }}">إلغاء</a></div>
</form>
@endsection
