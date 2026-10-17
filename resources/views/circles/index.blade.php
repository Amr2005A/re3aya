@extends('layouts.app')
@section('title','الحلقات والأنشطة')
@section('breadcrumbs')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">الحلقات</li>@endsection
@section('actions')@can('create', App\Models\Circle::class)<a href="{{ route('circles.create') }}" class="btn btn-primary">+ حلقة جديدة</a>@endcan @endsection
@section('content')
<div class="row g-3">
@forelse($circles as $c)
  <div class="col-12 col-md-6 col-xl-4"><div class="card h-100"><div class="card-body">
    <div class="d-flex justify-content-between"><h2 class="h6 fw-bold">{{ $c->name }}</h2><span class="badge {{ $c->is_active ? 'badge-soft-ok' : 'badge-soft-off' }}">{{ $c->is_active ? 'مفعّلة' : 'معطّلة' }}</span></div>
    <p class="small text-muted mb-2">{{ $c->description ?: '—' }}</p><div class="mb-3"><strong>{{ $c->criteria_count }}</strong> <span class="text-muted small">بند تقييم</span></div>
    <div class="d-flex gap-2"><a class="btn btn-sm btn-outline-primary" href="{{ route('circles.edit', $c) }}">{{ auth()->user()->can('circles.update') || auth()->user()->is_owner ? 'تعديل والبنود' : 'عرض البنود' }}</a>
    @can('delete', App\Models\Circle::class)<form method="POST" action="{{ route('circles.destroy', $c) }}" data-confirm="حذف الحلقة وبنودها؟ القيود القديمة تبقى محفوظة.">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">حذف</button></form>@endcan</div>
  </div></div></div>
@empty<div class="col-12"><div class="card"><div class="card-body text-center text-muted py-5">لا توجد حلقات بعد.</div></div></div>@endforelse
</div>
@endsection
