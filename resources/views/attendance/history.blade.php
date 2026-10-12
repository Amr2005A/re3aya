@extends('layouts.app')
@section('title','سجل الحضور')
@section('breadcrumbs')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">سجل الحضور</li>@endsection
@section('content')
<link href="{{ asset('css/attendance.css') }}" rel="stylesheet">
<form method="GET" class="row g-2 mb-3">
  <div class="col-6 col-md-2"><input type="date" name="from" value="{{ $from }}" class="form-control"></div>
  <div class="col-6 col-md-2"><input type="date" name="to" value="{{ $to }}" class="form-control"></div>
  <div class="col-12 col-md-3"><input name="q" value="{{ request('q') }}" class="form-control" placeholder="اسم الطالب…"></div>
  <div class="col-6 col-md-2"><select name="status" class="form-select"><option value="">كل الحالات</option>@foreach($statuses as $st)<option value="{{ $st->value }}" @selected(request('status')===$st->value)>{{ $st->label() }}</option>@endforeach</select></div>
  @if($seesAll)<div class="col-6 col-md-2"><select name="supervisor" class="form-select"><option value="">كل المشرفين</option>@foreach($supervisors as $s)<option value="{{ $s->id }}" @selected(request('supervisor')==$s->id)>{{ $s->user->name }}</option>@endforeach</select></div>@endif
  <div class="col-12 col-md-1"><button class="btn btn-secondary w-100">تطبيق</button></div>
</form>
<div class="card"><div class="table-responsive"><table class="table align-middle mb-0">
  <thead><tr><th>التاريخ</th><th>الطالب</th><th>الحالة</th><th class="d-none d-md-table-cell">المشرف</th><th class="d-none d-lg-table-cell">سجّلها</th><th class="d-none d-lg-table-cell">الوقت</th></tr></thead><tbody>
  @forelse($records as $r)<tr><td>{{ $r->date->format('Y-m-d') }}</td><td><a class="text-decoration-none" href="{{ route('students.show', $r->student_id) }}">{{ $r->student->name }}</a></td>
    <td><span class="badge badge-st-{{ $r->status->value }}">{{ $r->status->label() }}</span></td>
    <td class="d-none d-md-table-cell">{{ $r->supervisor?->user->name ?? '—' }}</td><td class="d-none d-lg-table-cell">{{ $r->recorder?->name ?? '—' }}</td><td class="d-none d-lg-table-cell small text-muted">{{ $r->recorded_at?->format('Y-m-d H:i') }}</td></tr>
  @empty<tr><td colspan="6" class="text-center text-muted py-5">لا توجد سجلات في هذه الفترة.</td></tr>@endforelse
  </tbody></table></div></div><div class="mt-3">{{ $records->links() }}</div>
@endsection
