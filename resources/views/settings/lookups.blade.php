@extends('layouts.app')
@section('title','الصفوف والشعب')
@section('breadcrumbs')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">الصفوف والشعب</li>@endsection
@section('content')
@php $canEdit = auth()->user()->is_owner || auth()->user()->can('settings.update'); @endphp
<div class="row g-3">
@foreach(['grades' => ['الصفوف', $grades], 'sections' => ['الشعب', $sections]] as $type => [$label, $rows])
  <div class="col-12 col-lg-6"><div class="card"><div class="card-header">{{ $label }}</div><div class="card-body">
    @foreach($rows as $r)
      <form method="POST" action="{{ route('lookups.update', [$type, $r->id]) }}" class="d-flex gap-2 mb-1">@csrf @method('PUT')
        <input name="name" value="{{ $r->name }}" class="form-control" @disabled(!$canEdit)><input name="sort_order" value="{{ $r->sort_order }}" type="number" min="0" class="form-control" style="max-width:80px" title="الترتيب" @disabled(!$canEdit)>
        @if($canEdit)<button class="btn btn-outline-secondary">حفظ</button>@endif
        <span class="badge align-self-center {{ $r->is_active ? 'badge-soft-ok' : 'badge-soft-off' }}">{{ $r->is_active ? 'مفعّل' : 'معطّل' }}</span>
      </form>
      @if($canEdit)<form method="POST" action="{{ route('lookups.toggle', [$type, $r->id]) }}" class="mb-3 text-end">@csrf @method('PATCH')<button class="btn btn-sm btn-link">{{ $r->is_active ? 'تعطيل' : 'تفعيل' }}</button></form>@endif
    @endforeach
    @if($canEdit)<form method="POST" action="{{ route('lookups.store', $type) }}" class="d-flex gap-2 mt-3 pt-3 border-top">@csrf
      <input name="name" class="form-control" placeholder="إضافة جديد…" required><input name="sort_order" type="number" min="0" class="form-control" style="max-width:80px" placeholder="0"><button class="btn btn-primary">إضافة</button></form>@endif
  </div></div></div>
@endforeach
</div>
@endsection
