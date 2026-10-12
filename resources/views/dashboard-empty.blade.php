@extends('layouts.app')
@section('title','أهلاً بك')
@section('breadcrumbs')<li class="breadcrumb-item active">الرئيسية</li>@endsection
@section('content')
<div class="card"><div class="card-body text-center py-5">
  <h2 class="h5">أهلاً {{ auth()->user()->name }}</h2>
  <p class="text-muted mb-0">حسابك مفعّل لكن لم تُمنح أي صلاحية بعد. تواصل مع مسؤول النظام ليحدد لك صلاحياتك.</p>
</div></div>
@endsection
