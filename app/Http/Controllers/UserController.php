<?php
namespace App\Http\Controllers;
use App\Actions\{SaveUser, SyncUserPermissions};
use App\Http\Requests\SaveUserRequest;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
class UserController extends Controller {
    public function index(Request $request) {
        Gate::authorize('viewAny', User::class);
        $users = User::query()
            ->when($request->q, fn ($q, $s) => $q->where(fn ($w) => $w->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%")->orWhere('username', 'like', "%$s%")))
            ->when($request->filled('active'), fn ($q) => $q->where('is_active', $request->boolean('active')))
            ->orderBy('name')->paginate(15)->withQueryString();
        return view('users.index', compact('users'));
    }
    public function create() { Gate::authorize('create', User::class); return view('users.form', ['user' => new User()]); }
    public function store(SaveUserRequest $request, SaveUser $save) {
        Gate::authorize('create', User::class);
        $user = $save($request->validated());
        return redirect()->route('users.permissions.edit', $user)->with('success', 'تم إنشاء المستخدم، حدّد صلاحياته الآن.');
    }
    public function edit(User $user) { Gate::authorize('update', $user); return view('users.form', compact('user')); }
    public function update(SaveUserRequest $request, User $user, SaveUser $save) {
        Gate::authorize('update', $user);
        $data = $request->validated();
        if (! Gate::allows('disable', $user)) unset($data['is_active']); // تغيير الحالة يتطلب صلاحية التعطيل
        $save($data, $user);
        return redirect()->route('users.index')->with('success', 'تم حفظ التعديلات.');
    }
    public function toggle(User $user) {
        Gate::authorize('disable', $user);
        $user->update(['is_active' => ! $user->is_active]);
        AuditLogger::log($user->is_active ? 'user.enabled' : 'user.disabled', $user);
        return back()->with('success', $user->is_active ? 'تم تفعيل المستخدم.' : 'تم تعطيل المستخدم.');
    }
    public function editPermissions(User $user) {
        Gate::authorize('managePermissions', $user);
        return view('users.permissions', [
            'user' => $user, 'groups' => config('riaya-permissions'),
            'current' => $user->getPermissionNames()->all(), 'grantable' => SyncUserPermissions::grantable(auth()->user()),
        ]);
    }
    public function updatePermissions(Request $request, User $user, SyncUserPermissions $sync) {
        Gate::authorize('managePermissions', $user);
        $data = $request->validate(['permissions' => ['array'], 'permissions.*' => ['string', Rule::in(SyncUserPermissions::grantable($request->user()))]]);
        $sync($request->user(), $user, $data['permissions'] ?? []);
        return redirect()->route('users.index')->with('success', 'تم تحديث الصلاحيات.');
    }
}
