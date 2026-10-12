@extends('layouts.app')
@section('title','تسجيل الحضور')
@section('breadcrumbs')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">الحضور</li>@endsection
@section('content')
<link href="{{ asset('css/attendance.css') }}" rel="stylesheet">
<form method="GET" class="row g-2 mb-3 align-items-end">
  <div class="col-7 col-md-3"><label class="form-label small mb-1">التاريخ</label><input type="date" name="date" value="{{ $date->toDateString() }}" max="{{ today()->toDateString() }}" class="form-control"></div>
  @if($seesAll)<div class="col-5 col-md-3"><label class="form-label small mb-1">المشرف</label><select name="supervisor" class="form-select"><option value="">الكل</option><option value="none" @selected(request('supervisor')==='none')>بدون مشرف</option>@foreach($supervisors as $s)<option value="{{ $s->id }}" @selected(request('supervisor')==$s->id)>{{ $s->user->name }}</option>@endforeach</select></div>@endif
  <div class="col-12 col-md-2"><button class="btn btn-secondary w-100">عرض</button></div>
  <div class="col-12 col-md-4 text-md-end small text-muted">{{ $date->translatedFormat('l j F Y') }} @if(!$isWorking)<span class="badge badge-soft-off">ليس يوم دوام حالياً</span>@endif</div>
</form>
@error('date')<div class="alert alert-danger py-2">{{ $message }}</div>@enderror
@if($closed)<div class="alert alert-warning">هذا اليوم ليس من أيام الدوام ولا توجد له سجلات، لذلك التسجيل غير متاح. يمكن تعديل أيام الدوام من الإعدادات.</div>@endif
<form method="POST" action="{{ route('attendance.store') }}">@csrf
  <input type="hidden" name="date" value="{{ $date->toDateString() }}"><input type="hidden" name="supervisor" value="{{ request('supervisor') }}">
  @if($canEdit)<div class="d-flex justify-content-between align-items-center mb-2 sticky-top bg-white border rounded p-2" style="top:0;z-index:5">
    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="document.querySelectorAll('input[value=present]:not(:disabled)').forEach(r=>{if(!document.querySelector('input[name=\''+r.name+'\']:checked'))r.checked=true})">تحديد غير المسجَّلين حاضرين</button>
    <button class="btn btn-primary">حفظ الحضور</button></div>@endif
  @forelse($students as $s)
    @php $rec = $records[$s->id] ?? null; $locked = $rec && ! $canUpdate; @endphp
    <div class="card mb-2 att-row"><div class="card-body py-2">
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div><a class="fw-bold text-decoration-none" href="{{ route('students.show', $s) }}">{{ $s->name }}</a> <small class="text-muted">{{ $s->gradeLevel?->name }} {{ $s->section?->name }}</small></div>
        <div class="btn-group" role="group">
          @foreach($statuses as $st)
            <input type="radio" class="btn-check" name="rows[{{ $s->id }}][status]" id="r{{ $s->id }}{{ $st->value }}" value="{{ $st->value }}" @checked($rec?->status === $st) @disabled(! $canEdit || $locked)>
            <label class="btn btn-sm att-opt {{ $st->css() }}" for="r{{ $s->id }}{{ $st->value }}">{{ $st->label() }}</label>
          @endforeach
        </div>
      </div>
      <input name="rows[{{ $s->id }}][note]" value="{{ $rec?->note }}" class="form-control form-control-sm mt-2" placeholder="ملاحظة (اختياري)" @disabled(! $canEdit || $locked)>
      @if($locked)<div class="small text-muted mt-1">مسجَّل مسبقاً — تعديله يحتاج صلاحية تعديل الحضور.</div>@endif
    </div></div>
  @empty<div class="card"><div class="card-body text-center text-muted py-5">لا يوجد طلاب لعرضهم.</div></div>@endforelse
</form>
@endsection
