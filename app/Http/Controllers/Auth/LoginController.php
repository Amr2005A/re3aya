<?php
namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Services\AuditLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
class LoginController extends Controller {
    public function show() { return view('auth.login'); }
    public function store(Request $request) {
        $data = $request->validate(['login' => ['required', 'string'], 'password' => ['required', 'string']]);
        $field = filter_var($data['login'], FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $ok = Auth::attempt([$field => $data['login'], 'password' => $data['password'], 'is_active' => true], $request->boolean('remember'));
        if (! $ok) return back()->withInput($request->only('login'))->withErrors(['login' => 'بيانات الدخول غير صحيحة أو الحساب معطّل.']);
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
