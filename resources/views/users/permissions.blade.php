@extends('layouts.app')
@section('title','صلاحيات: '.$user->name)
@section('breadcrumbs')<li class="breadcrumb-item"><a href="{{ route('users.index') }}">المستخدمون</a></li><li class="breadcrumb-item active">الصلاحيات</li>@endsection
@section('content')
<form method="POST" action="{{ route('users.permissions.update', $user) }}">@csrf @method('PUT')
  <div class="row g-3">
  @foreach($groups as $key => $g)
    <div class="col-12 col-md-6 col-xl-4"><div class="card perm-group h-100">
      <div class="card-header d-flex justify-content-between align-items-center">{{ $g['label'] }}
        <label class="form-check small fw-normal mb-0"><input type="checkbox" class="form-check-input" onchange="this.closest('.card').querySelectorAll('.p:not(:disabled)').forEach(c=>c.checked=this.checked)"> الكل</label></div>
      <div class="card-body">
      @foreach($g['items'] as $perm => $label)
        <div class="form-check mb-2"><input class="form-check-input p" type="checkbox" name="permissions[]" value="{{ $perm }}" id="{{ $perm }}"
          @checked(in_array($perm, old('permissions', $current))) @disabled(! in_array($perm, $grantable))>
          <label class="form-check-label" for="{{ $perm }}">{{ $label }}</label></div>
      @endforeach
      </div></div></div>
  @endforeach
  </div>
  <p class="small text-muted mt-3">الصلاحيات المعطّلة لا تملكها أنت، ولذلك لا يمكنك منحها.</p>
  <button class="btn btn-primary">حفظ الصلاحيات</button>
</form>
@endsection
