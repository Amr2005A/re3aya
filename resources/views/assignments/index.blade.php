@extends('layouts.app')
@section('title','توزيع الطلاب')
@section('breadcrumbs')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">توزيع الطلاب</li>@endsection
@section('content')
<div class="row g-3">
  <div class="col-12 col-lg-4"><div class="card"><div class="card-header">المشرفون</div><div class="list-group list-group-flush">
    <a class="list-group-item list-group-item-action d-flex justify-content-between {{ $from==='none' ? 'active' : '' }}" href="{{ route('assignments.index', ['from'=>'none']) }}">بدون مشرف <span class="badge bg-secondary">{{ $unassigned }}</span></a>
    @foreach($supervisors as $s)
      <a class="list-group-item list-group-item-action d-flex justify-content-between {{ (string)$from===(string)$s->id ? 'active' : '' }}" href="{{ route('assignments.index', ['from'=>$s->id]) }}">{{ $s->user->name }}@unless($s->is_active) <small>(معطّل)</small>@endunless <span class="badge bg-secondary">{{ $s->students_count }}</span></a>
    @endforeach</div></div></div>
  <div class="col-12 col-lg-8">
  @if($from)
    <form method="POST" action="{{ route('assignments.transfer') }}" class="card">@csrf<input type="hidden" name="from" value="{{ $from }}">
      <div class="card-header d-flex justify-content-between align-items-center">اختر الطلاب للنقل <label class="form-check small fw-normal mb-0"><input type="checkbox" class="form-check-input" onchange="document.querySelectorAll('.sid').forEach(c=>c.checked=this.checked)"> تحديد الكل</label></div>
      <div class="card-body">
        @error('student_ids')<div class="alert alert-danger py-2">{{ $message }}</div>@enderror
        @forelse($students as $st)
          <div class="form-check py-1"><input class="form-check-input sid" type="checkbox" name="student_ids[]" value="{{ $st->id }}" id="s{{ $st->id }}"><label class="form-check-label" for="s{{ $st->id }}">{{ $st->name }} <small class="text-muted">{{ $st->gradeLevel?->name }} {{ $st->section?->name }}</small></label></div>
        @empty<div class="text-muted text-center py-4">لا طلاب هنا.</div>@endforelse
      </div>
      @if($students->isNotEmpty())<div class="card-footer bg-transparent row g-2">
        <div class="col-md-5"><select name="to_supervisor_id" class="form-select"><option value="">بدون مشرف</option>@foreach($supervisors->where('is_active', true) as $s)<option value="{{ $s->id }}">نقل إلى: {{ $s->user->name }}</option>@endforeach</select></div>
        <div class="col-md-5"><input name="reason" class="form-control" placeholder="سبب النقل (اختياري)"></div>
        <div class="col-md-2"><button class="btn btn-primary w-100">نقل</button></div></div>@endif
    </form>
  @else<div class="card"><div class="card-body text-center text-muted py-5">اختر مشرفاً من القائمة لعرض طلابه ونقلهم.</div></div>@endif
  </div>
</div>
@endsection
