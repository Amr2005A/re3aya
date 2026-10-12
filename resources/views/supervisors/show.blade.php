@extends('layouts.app')
@section('title','طلاب المشرف: '.$supervisor->user->name)
@section('breadcrumbs')<li class="breadcrumb-item"><a href="{{ route('supervisors.index') }}">المشرفون</a></li><li class="breadcrumb-item active">{{ $supervisor->user->name }}</li>@endsection
@section('actions')@can('assign', App\Models\Supervisor::class)<a class="btn btn-primary" href="{{ route('assignments.index', ['from' => $supervisor->id]) }}">نقل طلاب</a>@endcan @endsection
@section('content')
<div class="card"><div class="table-responsive"><table class="table mb-0 align-middle"><thead><tr><th>الطالب</th><th>الصف/الشعبة</th><th>التواصل</th></tr></thead><tbody>
@forelse($students as $s)<tr><td>@can('view', $s)<a href="{{ route('students.show', $s) }}">{{ $s->name }}</a>@else{{ $s->name }}@endcan</td><td>{{ $s->gradeLevel?->name ?? '—' }} {{ $s->section ? '/ '.$s->section->name : '' }}</td><td><x-whatsapp :phone="$s->phone" /></td></tr>
@empty<tr><td colspan="3" class="text-center text-muted py-5">لا طلاب لدى هذا المشرف.</td></tr>@endforelse
</tbody></table></div></div><div class="mt-3">{{ $students->links() }}</div>
@endsection
