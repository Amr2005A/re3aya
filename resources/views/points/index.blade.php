@extends('layouts.app')
@section('title','سجل النقاط')
@section('breadcrumbs')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">النقاط</li>@endsection
@section('actions')@if(auth()->user()->is_owner || auth()->user()->can('points.award'))<a href="{{ route('points.create') }}" class="btn btn-primary">+ منح نقاط</a>@endif @endsection
@section('content')
<link href="{{ asset('css/attendance.css') }}" rel="stylesheet">
@error('reverse')<div class="alert alert-danger py-2">{{ $message }}</div>@enderror
<form method="GET" class="row g-2 mb-3">
  <div class="col-6 col-md-2"><select name="circle" class="form-select"><option value="">كل الحلقات</option>@foreach($circles as $c)<option value="{{ $c->id }}" @selected(request('circle')==$c->id)>{{ $c->name }}</option>@endforeach</select></div>
  <div class="col-6 col-md-2"><select name="type" class="form-select"><option value="">كل الأنواع</option>@foreach($types as $t)<option value="{{ $t->value }}" @selected(request('type')===$t->value)>{{ $t->label() }}</option>@endforeach</select></div>
  <div class="col-6 col-md-2"><select name="by" class="form-select"><option value="">كل المانحين</option>@foreach($awarders as $a)<option value="{{ $a->id }}" @selected(request('by')==$a->id)>{{ $a->name }}</option>@endforeach</select></div>
  <div class="col-6 col-md-2"><input type="date" name="from" value="{{ request('from') }}" class="form-control"></div>
  <div class="col-6 col-md-2"><input type="date" name="to" value="{{ request('to') }}" class="form-control"></div>
  <div class="col-6 col-md-2"><button class="btn btn-secondary w-100">تطبيق</button></div>
  @if(request('student'))<input type="hidden" name="student" value="{{ request('student') }}">@endif
</form>
<div class="row g-2 mb-3 text-center">
  @foreach([['المكتسب',$totals->plus,'var(--r-secondary)'],['المخصوم/المعكوس',$totals->minus,'var(--r-accent)'],['الصافي',$totals->net,'var(--r-dark)'],['عدد القيود',$totals->c,'var(--r-dark)']] as [$l,$n,$col])
    <div class="col-6 col-md-3"><div class="card"><div class="card-body py-2"><div class="fs-4 fw-bold" style="color:{{ $col }}" dir="ltr">{{ $n }}</div><div class="small text-muted">{{ $l }}</div></div></div></div>
  @endforeach
</div>
<div class="card"><div class="card-body py-0">
  @forelse($rows as $tx) @include('partials.point-row', ['tx' => $tx, 'showStudent' => true]) @empty<div class="text-center text-muted py-5">لا توجد قيود مطابقة.</div>@endforelse
</div></div><div class="mt-3">{{ $rows->links() }}</div>
@endsection
