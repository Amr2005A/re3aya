@extends('layouts.app')
@section('title','إعدادات النقاط')
@section('breadcrumbs')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">إعدادات النقاط</li>@endsection
@section('content')
@php $canEdit = auth()->user()->is_owner || auth()->user()->can('settings.update'); @endphp
<form method="POST" action="{{ route('points-settings.update') }}" class="card" style="max-width:520px">@csrf @method('PUT')
  <div class="card-body">
    <div class="form-check form-switch fs-5 mb-3"><input type="hidden" name="allow_negative" value="0"><input class="form-check-input" type="checkbox" role="switch" name="allow_negative" value="1" id="an" @checked($allowNegative) @disabled(!$canEdit)><label class="form-check-label" for="an">السماح بخصم النقاط (قيم سالبة)</label></div>
    <label class="form-label">الحد الأقصى للعملية الواحدة (نقطة)</label>
    <input type="number" min="1" name="max_per_award" value="{{ old('max_per_award', $max) }}" class="form-control @error('max_per_award') is-invalid @enderror" @disabled(!$canEdit)>@error('max_per_award')<div class="invalid-feedback">{{ $message }}</div>@enderror
    <p class="small text-muted mt-3 mb-0">إن كان الخصم غير مفعّل فتصحيح الأخطاء يتم بـ «عكس قيد» فقط. التغيير لا يمس القيود السابقة.</p>
  </div>
  @if($canEdit)<div class="card-footer bg-transparent"><button class="btn btn-primary">حفظ</button></div>@endif
</form>
@endsection
