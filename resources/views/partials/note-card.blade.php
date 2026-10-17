{{-- المتغيرات: $note، و $showStudent (اختياري) --}}
<div class="card mb-3 border-0 border-start border-4" style="border-color:var(--r-secondary)!important">
  <div class="card-body py-3">
    <div class="d-flex justify-content-between flex-wrap gap-1 mb-2 small text-muted">
      <span><strong style="color:var(--r-dark)">{{ $note->author?->name ?? 'مستخدم محذوف' }}</strong>
        @if(!empty($showStudent)) · <a href="{{ route('students.show', $note->student_id) }}" class="text-decoration-none">{{ $note->student?->name }}</a>@endif</span>
      <span>{{ $note->created_at->format('Y-m-d H:i') }} @if($note->wasEdited())<span class="badge badge-soft-off">عُدِّلت</span>@endif</span>
    </div>
    <div style="white-space:pre-line">{{ $note->body }}</div>
    @canany(['update', 'delete'], $note)
      <div class="d-flex gap-2 mt-2">
        @can('update', $note)<button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#ne{{ $note->id }}">تعديل</button>@endcan
        @can('delete', $note)<form method="POST" action="{{ route('notes.destroy', $note) }}" data-confirm="حذف هذه الملاحظة؟">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">حذف</button></form>@endcan
      </div>
      @can('update', $note)
        <form method="POST" action="{{ route('notes.update', $note) }}" class="collapse mt-2" id="ne{{ $note->id }}">@csrf @method('PUT')
          <textarea name="body" rows="3" class="form-control mb-2" required>{{ $note->body }}</textarea><button class="btn btn-sm btn-primary">حفظ التعديل</button>
        </form>
      @endcan
    @endcanany
  </div>
</div>
