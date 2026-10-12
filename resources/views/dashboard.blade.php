@extends('layouts.app')
@section('title','لوحة التحكم')
@section('breadcrumbs')<li class="breadcrumb-item active">الرئيسية</li>@endsection
@section('content')
<div class="card"><div class="card-body text-center py-5">
  <h2 class="h5">أهلاً {{ auth()->user()->name }}</h2>
  <p class="text-muted mb-0">ستظهر مؤشرات اللوحة في المرحلة 11 بعد اكتمال الوحدات.</p>
</div></div>
@endsection
