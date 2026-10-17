@extends('layouts.app')
@section('title','الملاحظات التربوية')
@section('breadcrumbs')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">الملاحظات</li>@endsection
@section('content')
<form method="GET" class="row g-2 mb-3">
  <div class="col-12 col-md-3"><input name="q" value="{{ request('q') }}" class="form-control" placeholder="بحث في نص الملاحظة…"></div>
  <div class="col-6 col-md-2"><select name="author" class="form-select"><option value="">كل الكتّاب</option>@foreach($authors as $a)<option value="{{ $a->id }}" @selected(request('author')==$a->id)>{{ $a->name }}</option>@endforeach</select></div>
  <div class="col-6 col-md-2"><input type="date" name="from" value="{{ request('from') }}" class="form-control"></div>
  <div class="col-6 col-md-2"><input type="date" name="to" value="{{ request('to') }}" class="form-control"></div>
  <div class="col-6 col-md-1"><button class="btn btn-secondary w-100">تطبيق</button></div>
  @if(request('student'))<input type="hidden" name="student" value="{{ request('student') }}"><div class="col-12 col-md-2"><a class="btn btn-light w-100" href="{{ route('notes.index') }}">كل الطلاب</a></div>@endif
</form>
@forelse($notes as $n) @include('partials.note-card', ['note' => $n, 'showStudent' => true]) @empty
  <div class="card"><div class="card-body text-center text-muted py-5">لا توجد ملاحظات مطابقة.</div></div>
@endforelse
<div class="mt-3">{{ $notes->links() }}</div>
@endsection
