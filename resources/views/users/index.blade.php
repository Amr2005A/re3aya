@extends('layouts.app')
@section('title','المستخدمون')
@section('breadcrumbs')<li class="breadcrumb-item"><a href="{{ route('dashboard') }}">الرئيسية</a></li><li class="breadcrumb-item active">المستخدمون</li>@endsection
@section('actions')@can('create', App\Models\User::class)<a href="{{ route('users.create') }}" class="btn btn-primary">+ مستخدم جديد</a>@endcan @endsection
@section('content')
@if($pending)<div class="alert alert-warning py-2">لديك <strong>{{ $pending }}</strong> حساب بانتظار المراجعة. <a href="{{ route('users.index', ['active' => 0]) }}">عرضها</a> ثم فعّل الحساب وحدّد صلاحياته.</div>@endif
<form class="row g-2 mb-3" method="GET">
  <div class="col-12 col-md-5"><input name="q" value="{{ request('q') }}" class="form-control" placeholder="بحث بالاسم أو البريد…"></div>
  <div class="col-6 col-md-3"><select name="active" class="form-select"><option value="">كل الحالات</option>
    <option value="1" @selected(request('active')==='1')>مفعّل</option><option value="0" @selected(request('active')==='0')>معطّل</option></select></div>
  <div class="col-6 col-md-2"><button class="btn btn-secondary w-100">تصفية</button></div>
</form>
<div class="card"><div class="table-responsive"><table class="table align-middle mb-0">
  <thead><tr><th>الاسم</th><th class="d-none d-md-table-cell">البريد</th><th>الحالة</th><th class="d-none d-md-table-cell">آخر دخول</th><th></th></tr></thead>
  <tbody>
  @forelse($users as $u)
    <tr>
      <td>{{ $u->name }} @if($u->is_owner)<span class="badge bg-dark">المالك</span>@endif</td>
      <td class="d-none d-md-table-cell">{{ $u->email }}</td>
      <td><span class="badge {{ $u->is_active ? 'badge-soft-ok' : 'badge-soft-off' }}">{{ $u->is_active ? 'مفعّل' : 'معطّل' }}</span></td>
      <td class="d-none d-md-table-cell small text-muted">{{ $u->last_login_at?->diffForHumans() ?? '—' }}</td>
      <td class="text-end text-nowrap">
        @can('update', $u)<a class="btn btn-sm btn-outline-secondary" href="{{ route('users.edit', $u) }}">تعديل</a>@endcan
        @can('managePermissions', $u)<a class="btn btn-sm btn-outline-primary" href="{{ route('users.permissions.edit', $u) }}">الصلاحيات</a>@endcan
        @can('disable', $u)
          <form method="POST" action="{{ route('users.toggle', $u) }}" class="d-inline" data-confirm="{{ $u->is_active ? 'تعطيل هذا المستخدم؟' : 'تفعيل هذا المستخدم؟' }}">@csrf @method('PATCH')
            <button class="btn btn-sm {{ $u->is_active ? 'btn-accent' : 'btn-secondary' }}">{{ $u->is_active ? 'تعطيل' : 'تفعيل' }}</button></form>
        @endcan
      </td>
    </tr>
  @empty
    <tr><td colspan="5" class="text-center text-muted py-5">لا يوجد مستخدمون مطابقون.</td></tr>
  @endforelse
  </tbody></table></div></div>
<div class="mt-3">{{ $users->links() }}</div>
@endsection
