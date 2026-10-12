@extends('layouts.app')
@section('title', $student->exists ? 'تعديل الطالب' : 'طالب جديد')
@section('breadcrumbs')<li class="breadcrumb-item"><a href="{{ route('students.index') }}">الطلاب</a></li><li class="breadcrumb-item active">@yield('title')</li>@endsection
@section('content')
@php $f = fn($k) => old($k, $student->$k); $statusVal = old('status', $student->status instanceof \BackedEnum ? $student->status->value : $student->status); @endphp
<form method="POST" class="card" style="max-width:820px" action="{{ $student->exists ? route('students.update', $student) : route('students.store') }}">
  @csrf @if($student->exists) @method('PUT') @endif
  <div class="card-body row g-3">
    @foreach([['name','الاسم',true],['father_name','اسم الأب',false],['phone','رقم التواصل الأساسي',true],['phone_extra','رقم تواصل إضافي',false],['father_job','عمل الأب',false],['address','السكن',false]] as [$k,$l,$req])
      <div class="col-md-6"><label class="form-label">{{ $l }} @if($req)<span class="text-danger">*</span>@endif</label>
        <input name="{{ $k }}" value="{{ $f($k) }}" class="form-control @error($k) is-invalid @enderror" @if(str_starts_with($k,'phone')) dir="ltr" inputmode="tel" @endif @required($req)>
        @error($k)<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    @endforeach
    <div class="col-md-4"><label class="form-label">الصف</label><select name="grade_level_id" class="form-select"><option value="">—</option>@foreach($grades as $g)<option value="{{ $g->id }}" @selected($f('grade_level_id')==$g->id)>{{ $g->name }}</option>@endforeach</select></div>
    <div class="col-md-4"><label class="form-label">الشعبة</label><select name="section_id" class="form-select"><option value="">—</option>@foreach($sections as $s)<option value="{{ $s->id }}" @selected($f('section_id')==$s->id)>{{ $s->name }}</option>@endforeach</select></div>
    <div class="col-md-4"><label class="form-label">تاريخ التسجيل</label><input type="date" name="registered_at" value="{{ old('registered_at', $student->registered_at?->toDateString() ?? now()->toDateString()) }}" class="form-control"></div>
    @if(auth()->user()->is_owner || auth()->user()->can('supervisors.assign-students') || auth()->user()->can('students.view-all'))
      <div class="col-md-6"><label class="form-label">المشرف / المربي</label><select name="supervisor_id" class="form-select"><option value="">بدون مشرف</option>@foreach($supervisors as $s)<option value="{{ $s->id }}" @selected($f('supervisor_id')==$s->id)>{{ $s->user->name }}</option>@endforeach</select></div>
    @endif
    <div class="col-md-6"><label class="form-label">حالة الطالب</label><select name="status" class="form-select">@foreach($statuses as $st)<option value="{{ $st->value }}" @selected($statusVal===$st->value)>{{ $st->label() }}</option>@endforeach</select></div>
  </div>
  <div class="card-footer bg-transparent d-flex gap-2"><button class="btn btn-primary">حفظ</button><a class="btn btn-light" href="{{ route('students.index') }}">إلغاء</a></div>
</form>
@endsection
