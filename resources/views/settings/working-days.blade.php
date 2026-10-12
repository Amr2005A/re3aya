@extends('layouts.app')
@section('title','أيام الدوام')
@section('breadcrumbs')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">أيام الدوام</li>@endsection
@section('content')
@php $canEdit = auth()->user()->is_owner || auth()->user()->can('settings.update'); @endphp
<form method="POST" action="{{ route('working-days.update') }}" class="card" style="max-width:520px">@csrf @method('PUT')
  <div class="card-body">
    @error('days')<div class="alert alert-danger py-2">{{ $message }}</div>@enderror
    @foreach($weekdays as $d)
      <div class="form-check form-switch fs-5 mb-2"><input class="form-check-input" type="checkbox" role="switch" name="days[]" value="{{ $d->value }}" id="d{{ $d->value }}" @checked(in_array($d->value, old('days', $active))) @disabled(! $canEdit)>
        <label class="form-check-label" for="d{{ $d->value }}">{{ $d->label() }}</label></div>
    @endforeach
    <p class="small text-muted mb-0 mt-3">تغيير أيام الدوام يؤثر على التسجيل المستقبلي فقط، ولا يعدّل ولا يحذف أي سجل حضور سابق.</p>
  </div>
  @if($canEdit)<div class="card-footer bg-transparent"><button class="btn btn-primary">حفظ</button></div>@endif
</form>
@endsection
