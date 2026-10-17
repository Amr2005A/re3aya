@php
  // تُضاف بقية الوحدات هنا مع كل مرحلة. العنصر يظهر لمن يملك أياً من الصلاحيات المذكورة.
  $items = [
    ['route'=>'dashboard','label'=>'لوحة التحكم','can'=>['dashboard.view'],'match'=>'dashboard','icon'=>'◧'],
    ['route'=>'students.index','label'=>'الطلاب','can'=>['students.view-all','students.view-own'],'match'=>'students.*','icon'=>'🎓'],
    ['route'=>'attendance.index','label'=>'تسجيل الحضور','can'=>['attendance.record','attendance.update','attendance.view-all','attendance.view-own'],'match'=>'attendance.index','icon'=>'✓'],
    ['route'=>'attendance.history','label'=>'سجل الحضور','can'=>['attendance.view-all','attendance.view-own','attendance.record','attendance.update'],'match'=>'attendance.history','icon'=>'🗓'],
    ['route'=>'attendance.report','label'=>'تقرير الحضور','can'=>['attendance.report'],'match'=>'attendance.report','icon'=>'📊'],
    ['route'=>'notes.index','label'=>'الملاحظات','can'=>['notes.view-all','notes.view-own'],'match'=>'notes.*','icon'=>'📝'],
    ['route'=>'circles.index','label'=>'الحلقات والأنشطة','can'=>['circles.view'],'match'=>'circles.*','icon'=>'◎'],
    ['route'=>'points.index','label'=>'النقاط','can'=>['points.view'],'match'=>'points.*','icon'=>'★'],
    ['route'=>'supervisors.index','label'=>'المشرفون','can'=>['supervisors.view'],'match'=>'supervisors.*','icon'=>'🧭'],
    ['route'=>'assignments.index','label'=>'توزيع الطلاب','can'=>['supervisors.assign-students'],'match'=>'assignments.*','icon'=>'⇄'],
    ['route'=>'users.index','label'=>'المستخدمون','can'=>['users.view'],'match'=>'users.*','icon'=>'👥'],
    ['route'=>'working-days.edit','label'=>'أيام الدوام','can'=>['settings.view'],'match'=>'working-days.*','icon'=>'📅'],
    ['route'=>'points-settings.edit','label'=>'إعدادات النقاط','can'=>['settings.view'],'match'=>'points-settings.*','icon'=>'⚙'],
    ['route'=>'lookups.index','label'=>'الصفوف والشعب','can'=>['settings.view'],'match'=>'lookups.*','icon'=>'⚙'],
  ];
  $u = auth()->user();
@endphp
<aside class="r-sidebar" id="sb">
  <div class="brand">رِعاية <span>●</span></div>
  <nav class="nav flex-column mt-2">
    @foreach($items as $i)
      @if($u->is_owner || collect($i['can'])->contains(fn($c) => $u->can($c)))
        <a class="nav-link {{ request()->routeIs($i['match']) ? 'active' : '' }}" href="{{ route($i['route']) }}"><span>{{ $i['icon'] }}</span>{{ $i['label'] }}</a>
      @endif
    @endforeach
  </nav>
</aside>
