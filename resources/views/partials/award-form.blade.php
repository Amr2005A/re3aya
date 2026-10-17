{{-- $circles، و $students (اختياري)، و $student (ثابت)، و $selected، و $fromStudent --}}
<form method="POST" action="{{ route('points.store') }}" class="card mb-3" id="awardForm">@csrf
  @if(!empty($fromStudent))<input type="hidden" name="from_student" value="1">@endif
  <div class="card-header">منح نقاط</div>
  <div class="card-body row g-3">
    @if(!empty($student))<input type="hidden" name="student_id" value="{{ $student->id }}">
    @else<div class="col-md-6"><label class="form-label">الطالب</label><select name="student_id" class="form-select @error('student_id') is-invalid @enderror" required><option value="">اختر…</option>@foreach($students as $s)<option value="{{ $s->id }}" @selected(old('student_id', $selected ?? null)==$s->id)>{{ $s->name }}</option>@endforeach</select>@error('student_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>@endif
    <div class="col-md-3"><label class="form-label">الحلقة</label><select name="circle_id" id="aCircle" class="form-select"><option value="">—</option>@foreach($circles as $c)<option value="{{ $c->id }}" @selected(old('circle_id')==$c->id)>{{ $c->name }}</option>@endforeach</select></div>
    <div class="col-md-3"><label class="form-label">البند</label><select name="criteria_id" id="aCrit" class="form-select @error('criteria_id') is-invalid @enderror"><option value="">— بدون بند —</option>
      @foreach($circles as $c)@foreach($c->activeCriteria as $k)<option value="{{ $k->id }}" data-circle="{{ $c->id }}" data-points="{{ $k->default_points }}" data-name="{{ $k->name }}" @selected(old('criteria_id')==$k->id)>{{ $k->name }} ({{ $k->default_points }})</option>@endforeach @endforeach</select>@error('criteria_id')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-3"><label class="form-label">عدد النقاط</label><input type="number" name="points" id="aPoints" value="{{ old('points') }}" class="form-control @error('points') is-invalid @enderror" required>@error('points')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-5"><label class="form-label">السبب</label><input name="reason" id="aReason" value="{{ old('reason') }}" class="form-control @error('reason') is-invalid @enderror" placeholder="مثال: مشاركة في الحلقة">@error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
    <div class="col-md-4"><label class="form-label">ملاحظة (اختياري)</label><input name="note" value="{{ old('note') }}" class="form-control"></div>
  </div>
  <div class="card-footer bg-transparent"><button class="btn btn-primary">تسجيل النقاط</button></div>
</form>
<script>(function(){const c=document.getElementById('aCircle'),k=document.getElementById('aCrit'),p=document.getElementById('aPoints'),r=document.getElementById('aReason');if(!c||!k)return;
function filter(){[...k.options].forEach(o=>{if(!o.value)return;o.hidden=!!c.value&&o.dataset.circle!==c.value;});if(k.selectedOptions[0]&&k.selectedOptions[0].hidden)k.value='';}
c.addEventListener('change',filter);k.addEventListener('change',()=>{const o=k.selectedOptions[0];if(!o||!o.value)return;if(!c.value)c.value=o.dataset.circle;p.value=o.dataset.points;r.placeholder=o.dataset.name;});filter();})();</script>
