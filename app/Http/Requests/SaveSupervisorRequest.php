<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
class SaveSupervisorRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        $uid = $this->route('supervisor')?->user_id;
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($uid)],
            'username' => ['nullable', 'alpha_dash', 'max:60', Rule::unique('users', 'username')->ignore($uid)],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[0-9+\s\-()]{6,}$/'],
            'password' => [$uid ? 'nullable' : 'required', 'confirmed', Password::min(8)],
        ];
    }
}
