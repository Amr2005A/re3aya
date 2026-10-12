@extends('layouts.app')
@section('title','تقرير الحضور')
@section('breadcrumbs')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">تقرير الحضور</li>@endsection
@section('content')
<link href="{{ asset('css/attendance.css') }}" rel="stylesheet">
@php use App\Services\AttendanceStats; @endphp
<form method="GET" class="row g-2 mb-3">
  <div class="col-6 col-md-2"><input type="date" name="from" value="{{ $from }}" class="form-control"></div>
  <div class="col-6 col-md-2"><input type="date" name="to" value="{{ $to }}" class="form-control"></div>
  @if($seesAll)<div class="col-6 col-md-3"><select name="supervisor" class="form-select"><option value="">كل المشرفين</option>@foreach($supervisors as $s)<option value="{{ $s->id }}" @selected(request('supervisor')==$s->id)>{{ $s->user->name }}</option>@endforeach</select></div>@endif
  <div class="col-6 col-md-3"><select name="sort" class="form-select"><option value="absent">الأكثر غياباً</option><option value="late" @selected(request('sort')==='late')>الأكثر تأخراً</option><option value="present" @selected(request('sort')==='present')>الأكثر حضوراً</option></select></div>
  <div class="col-12 col-md-2"><button class="btn btn-secondary w-100">تطبيق</button></div>
</form>
@php $t = $totals; @endphp
<div class="row g-2 mb-3 text-center">
  @foreach([['حاضر','present',$t->present_count],['غائب','absent',$t->absent_count],['متأخر','late',$t->late_count],['بعذر','excused',$t->excused_count]] as [$l,$k,$n])
    <div class="col-6 col-md-3"><div class="card"><div class="card-body py-2"><div class="fs-4 fw-bold">{{ (int) $n }}</div><span class="badge badge-st-{{ $k }}">{{ $l }}</span></div></div></div>
  @endforeach
  <div class="col-12"><div class="card"><div class="card-body py-2">نسبة الحضور العامة: <strong>{{ ($r = AttendanceStats::rate((int)$t->present_count,(int)$t->late_count,(int)$t->absent_count)) !== null ? $r.'%' : '—' }}</strong> <small class="text-muted">(حاضر + متأخر ÷ حاضر + متأخر + غائب، دون الأعذار)</small></div></div></div>
</div>
<div class="card"><div class="table-responsive"><table class="table align-middle mb-0">
  <thead><tr><th>الطالب</th><th>حاضر</th><th>غائب</th><th>متأخر</th><th>بعذر</th><th>النسبة</th></tr></thead><tbody>
  @forelse($rows as $x)<tr><td><a class="text-decoration-none" href="{{ route('students.show', $x->student_id) }}">{{ $x->student->name }}</a></td>
    <td>{{ $x->present_count }}</td><td>{{ $x->absent_count }}</td><td>{{ $x->late_count }}</td><td>{{ $x->excused_count }}</td>
    <td>{{ ($rt = AttendanceStats::rate((int)$x->present_count,(int)$x->late_count,(int)$x->absent_count)) !== null ? $rt.'%' : '—' }}</td></tr>
  @empty<tr><td colspan="6" class="text-center text-muted py-5">لا توجد بيانات في هذه الفترة.</td></tr>@endforelse
  </tbody></table></div></div><div class="mt-3">{{ $rows->links() }}</div>
@endsection
