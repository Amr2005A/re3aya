@extends('layouts.app')
@section('title','الطلاب')
@section('breadcrumbs')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">الطلاب</li>@endsection
@section('actions')@can('create', App\Models\Student::class)<a href="{{ route('students.create') }}" class="btn btn-primary">+ طالب جديد</a>@endcan @endsection
@section('content')
@php $seesAll = auth()->user()->is_owner || auth()->user()->can('students.view-all'); @endphp
<form class="row g-2 mb-3" method="GET">
  <div class="col-12 col-lg-3"><input name="q" value="{{ request('q') }}" class="form-control" placeholder="اسم الطالب أو الأب أو الهاتف…"></div>
  <div class="col-6 col-lg-2"><select name="grade" class="form-select"><option value="">كل الصفوف</option>@foreach($grades as $g)<option value="{{ $g->id }}" @selected(request('grade')==$g->id)>{{ $g->name }}</option>@endforeach</select></div>
  <div class="col-6 col-lg-2"><select name="section" class="form-select"><option value="">كل الشعب</option>@foreach($sections as $s)<option value="{{ $s->id }}" @selected(request('section')==$s->id)>{{ $s->name }}</option>@endforeach</select></div>
  @if($seesAll)<div class="col-6 col-lg-2"><select name="supervisor" class="form-select"><option value="">كل المشرفين</option><option value="none" @selected(request('supervisor')==='none')>بدون مشرف</option>@foreach($supervisors as $s)<option value="{{ $s->id }}" @selected(request('supervisor')==$s->id)>{{ $s->user->name }}</option>@endforeach</select></div>@endif
  <div class="col-6 col-lg-2"><select name="status" class="form-select"><option value="">كل الحالات</option>@foreach($statuses as $st)<option value="{{ $st->value }}" @selected(request('status')===$st->value)>{{ $st->label() }}</option>@endforeach</select></div>
  <div class="col-6 col-lg-2"><select name="sort" class="form-select"><option value="name">الفرز: الاسم</option><option value="registered_at" @selected(request('sort')==='registered_at')>تاريخ التسجيل</option><option value="points_balance" @selected(request('sort')==='points_balance')>النقاط</option></select></div>
  <div class="col-6 col-lg-1"><select name="dir" class="form-select"><option value="asc">↑</option><option value="desc" @selected(request('dir')==='desc')>↓</option></select></div>
  <div class="col-12 col-lg-1"><button class="btn btn-secondary w-100">تطبيق</button></div>
</form>
<div class="card"><div class="table-responsive"><table class="table align-middle mb-0">
  <thead><tr><th>الطالب</th><th class="d-none d-md-table-cell">الصف/الشعبة</th><th class="d-none d-lg-table-cell">المشرف</th><th>الحالة</th><th>التواصل</th><th class="d-none d-md-table-cell">النقاط</th></tr></thead>
  <tbody>
  @forelse($students as $s)
    <tr>
      <td><a class="fw-bold text-decoration-none" href="{{ route('students.show', $s) }}">{{ $s->name }}</a><div class="small text-muted">{{ $s->father_name }}</div></td>
      <td class="d-none d-md-table-cell">{{ $s->gradeLevel?->name ?? '—' }} {{ $s->section ? '/ '.$s->section->name : '' }}</td>
      <td class="d-none d-lg-table-cell">{{ $s->supervisor?->user->name ?? '—' }}</td>
      <td><span class="badge {{ $s->status->badgeClass() }}">{{ $s->status->label() }}</span></td>
      <td><x-whatsapp :phone="$s->phone" /></td>
      <td class="d-none d-md-table-cell">{{ $s->points_balance }}</td>
    </tr>
  @empty
    <tr><td colspan="6" class="text-center text-muted py-5">لا يوجد طلاب مطابقون.</td></tr>
  @endforelse
  </tbody></table></div></div>
<div class="mt-3">{{ $students->links() }}</div>
@endsection
