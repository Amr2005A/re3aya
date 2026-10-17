<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class LoginController extends Controller {
    public function show() { return view('auth.login'); }
    public function store(Request $request) {
        $data = $request->validate(['login' => ['required', 'string'], 'password' => ['required', 'string']]);
        $field = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $ok = Auth::attempt([$field => $data['login'], 'password' => $data['password'], 'is_active' => true], $request->boolean('remember'));
        if (! $ok) {
            $u = User::where($field, $data['login'])->first();
            // الرسالة المفصّلة تظهر فقط لمن أدخل كلمة المرور الصحيحة
            $msg = ($u && ! $u->is_active && Hash::check($data['password'], $u->password))
                ? 'حسابك بانتظار موافقة الإدارة أو معطّل. تواصل مع المسؤول.' : 'بيانات الدخول غير صحيحة.';
            return back()->withInput($request->only('login'))->withErrors(['login' => $msg]);
        }
        $request->session()->regenerate();
        $request->user()->forceFill(['last_login_at' => now()])->save();
        AuditLogger::log('auth.login', $request->user());
        return redirect()->intended(route('dashboard'));
    }
    public function destroy(Request $request) {
        Auth::logout(); $request->session()->invalidate(); $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
