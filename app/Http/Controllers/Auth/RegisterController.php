<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
class RegisterController extends Controller {
    private function guard(): void { abort_unless(config('riaya.registration_enabled'), 404); }
    public function show() { $this->guard(); return view('auth.register'); }
    public function store(Request $request) {
        $this->guard();
        $done = redirect()->route('login')->with('status', 'تم استلام طلبك. سيُفعَّل حسابك بعد موافقة الإدارة.');
        if ($request->filled('website')) return $done; // فخ للبوتات: حقل مخفي يجب أن يبقى فارغاً
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'username' => ['nullable', 'string', 'max:60', 'alpha_dash', 'unique:users,username'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);
        $user = new User($data);
        $user->is_active = false; // لا يدخل قبل موافقة الإدارة، وبلا أي صلاحية
        $user->save();
        AuditLogger::log('auth.registered', $user, null, $user->only(['name', 'email']));
        return $done;
    }
}
