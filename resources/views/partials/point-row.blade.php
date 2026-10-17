{{-- $tx، و $showStudent اختياري --}}
@php $rev = $tx->type === \App\Enums\PointTransactionType::Reversal; $reversed = $tx->reversedBy !== null; @endphp
<div class="d-flex justify-content-between align-items-start gap-3 py-2 border-bottom {{ $reversed ? 'opacity-50' : '' }}">
  <div>
    <span class="fw-bold fs-5" style="color:{{ $tx->points >= 0 ? 'var(--r-secondary)' : 'var(--r-accent)' }}" dir="ltr">{{ $tx->points > 0 ? '+' : '' }}{{ $tx->points }}</span>
    <span class="ms-2 {{ $reversed ? 'text-decoration-line-through' : '' }}">{{ $tx->reason }}</span>
    @if($rev)<span class="badge badge-soft-off">عكس</span>@endif @if($reversed)<span class="badge badge-soft-off">معكوس</span>@endif
    @if($tx->type === \App\Enums\PointTransactionType::Badge)<span class="badge badge-st-late">وسام</span>@endif
    <div class="small text-muted">
      @if(!empty($showStudent))<a href="{{ route('students.show', $tx->student_id) }}" class="text-decoration-none">{{ $tx->student?->name }}</a> · @endif
      @if($tx->circle){{ $tx->circle->name }}@if($tx->criterion) ← {{ $tx->criterion->name }}@endif · @endif
      {{ $tx->awarder?->name ?? '—' }} · {{ $tx->occurred_at->format('Y-m-d H:i') }}@if($tx->note) · {{ $tx->note }}@endif
    </div>
  </div>
  @can('reverse', $tx)@if($tx->isReversible())
    <form method="POST" action="{{ route('points.reverse', $tx) }}" data-confirm="عكس هذا القيد بقيد معاكس؟">@csrf<button class="btn btn-sm btn-outline-secondary text-nowrap">عكس</button></form>
  @endif @endcan
</div>
