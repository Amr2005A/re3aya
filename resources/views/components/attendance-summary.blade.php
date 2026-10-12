<div class="card"><div class="card-body">
  <div class="row text-center g-2 mb-3">
    @foreach([['حاضر',$p,'present'],['غائب',$a,'absent'],['متأخر',$l,'late'],['بعذر',$e,'excused']] as [$lb,$n,$k])
      <div class="col-3"><div class="fs-4 fw-bold">{{ $n }}</div><span class="badge badge-st-{{ $k }}">{{ $lb }}</span></div>
    @endforeach
  </div>
  <div class="text-center mb-3">نسبة الحضور: <strong>{{ $rate !== null ? $rate.'%' : '—' }}</strong> <small class="text-muted">(من {{ $total }} يوم مسجّل)</small></div>
  <table class="table table-sm mb-0"><tbody>
    @forelse($recent as $r)<tr><td>{{ $r->date->format('Y-m-d') }}</td><td><span class="badge badge-st-{{ $r->status->value }}">{{ $r->status->label() }}</span></td><td class="small text-muted">{{ $r->supervisor?->user->name }}</td><td class="small">{{ $r->note }}</td></tr>
    @empty<tr><td class="text-center text-muted py-4">لا توجد سجلات حضور.</td></tr>@endforelse
  </tbody></table>
</div></div>
