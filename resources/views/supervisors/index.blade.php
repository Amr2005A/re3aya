@extends('layouts.app')
@section('title','المشرفون')
@section('breadcrumbs')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">المشرفون</li>@endsection
@section('actions')@can('create', App\Models\Supervisor::class)<a href="{{ route('supervisors.create') }}" class="btn btn-primary">+ مشرف جديد</a>@endcan @endsection
@section('content')
<div class="row g-3">
@forelse($supervisors as $s)
  <div class="col-12 col-md-6 col-xl-4"><div class="card h-100"><div class="card-body">
    <div class="d-flex justify-content-between"><h2 class="h6 fw-bold mb-1">{{ $s->user->name }}</h2>
      <span class="badge {{ $s->is_active ? 'badge-soft-ok' : 'badge-soft-off' }}">{{ $s->is_active ? 'مفعّل' : 'معطّل' }}</span></div>
    <div class="display-6 fw-bold my-2" style="color:var(--r-secondary)">{{ $s->students_count }} <small class="fs-6 text-muted">طالب</small></div>
    <div class="mb-3 small"><x-whatsapp :phone="$s->phone" /></div>
    <div class="d-flex gap-2 flex-wrap">
      <a class="btn btn-sm btn-outline-primary" href="{{ route('supervisors.show', $s) }}">طلابه</a>
      @can('update', App\Models\Supervisor::class)<a class="btn btn-sm btn-outline-secondary" href="{{ route('supervisors.edit', $s) }}">تعديل</a>@endcan
      @can('assign', App\Models\Supervisor::class)<a class="btn btn-sm btn-outline-secondary" href="{{ route('assignments.index', ['from' => $s->id]) }}">نقل طلاب</a>@endcan
      @can('disable', App\Models\Supervisor::class)<form method="POST" action="{{ route('supervisors.toggle', $s) }}" data-confirm="{{ $s->is_active ? 'تعطيل المشرف ومنعه من الدخول؟' : 'تفعيل المشرف؟' }}">@csrf @method('PATCH')<button class="btn btn-sm {{ $s->is_active ? 'btn-accent' : 'btn-secondary' }}">{{ $s->is_active ? 'تعطيل' : 'تفعيل' }}</button></form>@endcan
    </div></div></div></div>
@empty<div class="col-12"><div class="card"><div class="card-body text-center text-muted py-5">لا يوجد مشرفون بعد.</div></div></div>@endforelse
</div>
@endsection
