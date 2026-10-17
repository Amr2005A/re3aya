<div>
  @if($canCreate)
    <form method="POST" action="{{ route('students.notes.store', $student) }}" class="card mb-3">@csrf
      <div class="card-body">
        <label class="form-label fw-bold">ملاحظة جديدة</label>
        <textarea name="body" rows="3" class="form-control @error('body') is-invalid @enderror" placeholder="اكتب ملاحظتك التربوية…" required>{{ old('body') }}</textarea>
        @error('body')<div class="invalid-feedback">{{ $message }}</div>@enderror
        <div class="d-flex justify-content-between align-items-center mt-2"><small class="text-muted">يظهر اسمك وتاريخ الملاحظة تلقائياً.</small><button class="btn btn-primary">إضافة</button></div>
      </div>
    </form>
  @endif
  @forelse($notes as $n) @include('partials.note-card', ['note' => $n]) @empty
    <div class="card"><div class="card-body text-center text-muted py-5">لا توجد ملاحظات لهذا الطالب بعد.</div></div>
  @endforelse
  @if($total > $notes->count())<div class="text-center"><a href="{{ route('notes.index', ['student' => $student->id]) }}">عرض كل الملاحظات ({{ $total }})</a></div>@endif
</div>
