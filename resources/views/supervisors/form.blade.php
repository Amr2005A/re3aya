@extends('layouts.app')
@section('title', $supervisor->exists ? 'تعديل مشرف' : 'مشرف جديد')
@section('breadcrumbs')<li class="breadcrumb-item"><a href="{{ route('supervisors.index') }}">المشرفون</a></li><li class="breadcrumb-item active">@yield('title')</li>@endsection
@section('content')
<form method="POST" class="card" style="max-width:640px" action="{{ $supervisor->exists ? route('supervisors.update', $supervisor) : route('supervisors.store') }}">
  @csrf @if($supervisor->exists) @method('PUT') @endif
  <div class="card-body row g-3">
    @foreach([['name','الاسم','text',$supervisor->user?->name],['email','البريد (لتسجيل الدخول)','email',$supervisor->user?->email],['username','اسم المستخدم (اختياري)','text',$supervisor->user?->username],['phone','رقم الهاتف','text',$supervisor->phone]] as [$k,$l,$t,$v])
      <div class="col-12"><label class="form-label">{{ $l }}</label><input type="{{ $t }}" name="{{ $k }}" value="{{ old($k, $v) }}" class="form-control @error($k) is-invalid @enderror" @required(in_array($k,['name','email']))>@error($k)<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    @endforeach
    <div class="col-md-6"><label class="form-label">كلمة المرور @if($supervisor->exists)<small class="text-muted">(فارغة = بدون تغيير)</small>@endif</label><input type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">@error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-6"><label class="form-label">تأكيد كلمة المرور</label><input type="password" name="password_confirmation" class="form-control"></div>
  </div>
  <div class="card-footer bg-transparent d-flex gap-2"><button class="btn btn-primary">حفظ</button><a class="btn btn-light" href="{{ route('supervisors.index') }}">إلغاء</a></div>
</form>
@endsection
