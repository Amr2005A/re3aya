@extends('layouts.app')
@section('title','منح نقاط')
@section('breadcrumbs')<li class="breadcrumb-item"><a href="{{ route('points.index') }}">النقاط</a></li><li class="breadcrumb-item active">منح</li>@endsection
@section('content')<div style="max-width:900px">@include('partials.award-form', ['students' => $students, 'selected' => $selected, 'circles' => $circles])</div>@endsection
