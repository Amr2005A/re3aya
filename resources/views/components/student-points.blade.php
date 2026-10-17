<div>
  <div class="row g-2 mb-3 text-center">
    <div class="col-6"><div class="card"><div class="card-body py-3"><div class="display-6 fw-bold" style="color:var(--r-secondary)">{{ $student->points_balance }}</div><div class="small text-muted">الرصيد الحالي</div></div></div></div>
    <div class="col-6"><div class="card"><div class="card-body py-3"><div class="display-6 fw-bold" dir="ltr">{{ $recent > 0 ? '+' : '' }}{{ $recent }}</div><div class="small text-muted">آخر 30 يوماً</div></div></div></div>
  </div>
  @error('reverse')<div class="alert alert-danger py-2">{{ $message }}</div>@enderror
  @if($canAward) @include('partials.award-form', ['student' => $student, 'circles' => $circles, 'fromStudent' => true]) @endif
  <div class="card"><div class="card-header">سجل النقاط</div><div class="card-body py-0">
    @forelse($rows as $tx) @include('partials.point-row', ['tx' => $tx]) @empty<div class="text-center text-muted py-5">لا توجد قيود نقاط بعد.</div>@endforelse
  </div>
  @if($rows->count() >= 30)<div class="card-footer bg-transparent text-center"><a href="{{ route('points.index', ['student' => $student->id]) }}">عرض كل القيود</a></div>@endif</div>
</div>
