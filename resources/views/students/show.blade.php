@extends('layouts.app')
@section('title', $student->name)
@section('breadcrumbs')<li class="breadcrumb-item"><a href="{{ route('students.index') }}">الطلاب</a></li><li class="breadcrumb-item active">{{ $student->name }}</li>@endsection
@section('actions')<div class="d-flex gap-2">
  @can('update', $student)<a class="btn btn-outline-secondary" href="{{ route('students.edit', $student) }}">تعديل</a>@endcan
  @can('delete', $student)<form method="POST" action="{{ route('students.destroy', $student) }}" data-confirm="حذف هذا الطالب؟ ستبقى سجلاته محفوظة.">@csrf @method('DELETE')<button class="btn btn-accent">حذف</button></form>@endcan</div>@endsection
@section('content')
<div class="card mb-3"><div class="card-body d-flex flex-wrap gap-4 align-items-center">
  <div><div class="small text-muted">المشرف</div><div class="fw-bold">{{ $student->supervisor?->user->name ?? 'بدون مشرف' }}</div></div>
  <div><div class="small text-muted">الحالة</div><span class="badge {{ $student->status->badgeClass() }}">{{ $student->status->label() }}</span></div>
  <div><div class="small text-muted">النقاط</div><div class="fw-bold">{{ $student->points_balance }}</div></div>
  <div><div class="small text-muted">التواصل</div><x-whatsapp :phone="$student->phone" /> @if($student->phone_extra)<div class="mt-1"><x-whatsapp :phone="$student->phone_extra" /></div>@endif</div>
</div></div>
<ul class="nav nav-tabs mb-3" role="tablist">
  @foreach(['info'=>'نظرة عامة','att'=>'الحضور','notes'=>'الملاحظات','pts'=>'النقاط','badges'=>'الأوسمة','fin'=>'المالية','tl'=>'السجل الزمني'] as $id => $l)
    <li class="nav-item"><button class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab" data-bs-target="#t-{{ $id }}" type="button">{{ $l }}</button></li>
  @endforeach
</ul>
<div class="tab-content">
  <div class="tab-pane fade show active" id="t-info"><div class="card"><div class="card-body"><dl class="row mb-0">
    @foreach(['اسم الأب'=>$student->father_name,'السكن'=>$student->address,'عمل الأب'=>$student->father_job,'الصف'=>$student->gradeLevel?->name,'الشعبة'=>$student->section?->name,'تاريخ التسجيل'=>$student->registered_at?->format('Y-m-d')] as $k => $v)
      <dt class="col-5 col-md-3 text-muted fw-normal">{{ $k }}</dt><dd class="col-7 col-md-9">{{ $v ?: '—' }}</dd>
    @endforeach</dl></div></div></div>
  @foreach(['notes'=>'الملاحظات (المرحلة 6)','pts'=>'النقاط (المرحلة 7)','badges'=>'الأوسمة (المرحلة 8)','fin'=>'المالية (المرحلة 9)'] as $id => $l)
    <div class="tab-pane fade" id="t-{{ $id }}"><div class="card"><div class="card-body text-center text-muted py-5">{{ $l }} — ستُفعَّل هذه التبويبة في مرحلتها.</div></div></div>
  @endforeach
  <div class="tab-pane fade" id="t-att"><link href="{{ asset('css/attendance.css') }}" rel="stylesheet">
    @can('viewAttendance', $student)<x-attendance-summary :student="$student" />@else<div class="card"><div class="card-body text-center text-muted py-5">لا تملك صلاحية عرض حضور هذا الطالب.</div></div>@endcan</div>
  <div class="tab-pane fade" id="t-tl"><div class="card"><div class="card-body">
    @forelse($timeline as $e)
      <div class="d-flex gap-3 mb-3"><div class="rounded-circle mt-1" style="width:10px;height:10px;background:var(--r-secondary);flex:none"></div>
        <div><div>{{ $e['text'] }}</div><div class="small text-muted">{{ $e['by'] ?? 'النظام' }} · {{ \Illuminate\Support\Carbon::parse($e['at'])->format('Y-m-d H:i') }}</div></div></div>
    @empty<div class="text-center text-muted py-4">لا توجد أحداث بعد.</div>@endforelse
  </div></div></div>
</div>
@endsection
