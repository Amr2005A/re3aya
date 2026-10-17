@extends('layouts.app')
@section('title', $circle->exists ? 'الحلقة: '.$circle->name : 'حلقة جديدة')
@section('breadcrumbs')<li class="breadcrumb-item"><a href="{{ route('circles.index') }}">الحلقات</a></li><li class="breadcrumb-item active">@yield('title')</li>@endsection
@section('content')
@php $canEdit = auth()->user()->is_owner || auth()->user()->can($circle->exists ? 'circles.update' : 'circles.create'); $canCrit = auth()->user()->is_owner || auth()->user()->can('circles.manage-criteria'); @endphp
<div class="row g-3">
<div class="col-12 col-lg-5"><form method="POST" class="card" action="{{ $circle->exists ? route('circles.update', $circle) : route('circles.store') }}">@csrf @if($circle->exists) @method('PUT') @endif
  <div class="card-body row g-3">
    <div class="col-12"><label class="form-label">اسم الحلقة</label><input name="name" value="{{ old('name', $circle->name) }}" class="form-control @error('name') is-invalid @enderror" required @disabled(!$canEdit)>@error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-12"><label class="form-label">الوصف</label><textarea name="description" rows="2" class="form-control" @disabled(!$canEdit)>{{ old('description', $circle->description) }}</textarea></div>
    <div class="col-6"><label class="form-label">الترتيب</label><input type="number" min="0" name="sort_order" value="{{ old('sort_order', $circle->sort_order ?? 0) }}" class="form-control" @disabled(!$canEdit)></div>
    <div class="col-6 d-flex align-items-end"><div class="form-check form-switch"><input type="hidden" name="is_active" value="0"><input class="form-check-input" type="checkbox" role="switch" name="is_active" value="1" id="ca" @checked(old('is_active', $circle->is_active)) @disabled(!$canEdit)><label class="form-check-label" for="ca">مفعّلة</label></div></div>
  </div>
  @if($canEdit)<div class="card-footer bg-transparent"><button class="btn btn-primary">حفظ</button></div>@endif
</form></div>
@if($circle->exists)
<div class="col-12 col-lg-7"><div class="card"><div class="card-header">بنود التقييم / التحفيز</div><div class="card-body">
  @error('default_points')<div class="alert alert-danger py-2">{{ $message }}</div>@enderror
  @foreach($circle->criteria as $k)
    <div class="border-bottom pb-2 mb-2 {{ $k->is_active ? '' : 'opacity-50' }}">
      <form method="POST" action="{{ route('criteria.update', $k) }}" class="row g-2 align-items-center">@csrf @method('PUT')
        <div class="col-12 col-md-5"><input name="name" value="{{ $k->name }}" class="form-control form-control-sm" @disabled(!$canCrit)></div>
        <div class="col-4 col-md-2"><input type="number" name="default_points" value="{{ $k->default_points }}" class="form-control form-control-sm" title="النقاط الافتراضية" @disabled(!$canCrit)></div>
        <div class="col-4 col-md-2"><input type="number" min="0" name="sort_order" value="{{ $k->sort_order }}" class="form-control form-control-sm" title="الترتيب" @disabled(!$canCrit)></div>
        @if($canCrit)<div class="col-4 col-md-3 d-flex gap-1"><button class="btn btn-sm btn-outline-secondary">حفظ</button></div>@endif
      </form>
      @if($canCrit)<div class="d-flex gap-2 mt-1">
        <form method="POST" action="{{ route('criteria.toggle', $k) }}">@csrf @method('PATCH')<button class="btn btn-sm btn-link p-0">{{ $k->is_active ? 'تعطيل' : 'تفعيل' }}</button></form>
        <form method="POST" action="{{ route('criteria.destroy', $k) }}" data-confirm="حذف البند؟ قيوده القديمة تبقى.">@csrf @method('DELETE')<button class="btn btn-sm btn-link text-danger p-0">حذف</button></form></div>@endif
    </div>
  @endforeach
  @if($canCrit)<form method="POST" action="{{ route('circles.criteria.store', $circle) }}" class="row g-2 pt-2">@csrf
    <div class="col-12 col-md-7"><input name="name" class="form-control" placeholder="بند جديد (مثال: حفظ القرآن)" required></div>
    <div class="col-6 col-md-3"><input type="number" name="default_points" class="form-control" placeholder="النقاط" required></div>
    <div class="col-6 col-md-2"><button class="btn btn-primary w-100">إضافة</button></div></form>@endif
</div></div></div>
@endif
</div>
@endsection
