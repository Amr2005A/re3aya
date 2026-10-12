<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rule;
class SaveUserRequest extends FormRequest {
    public function authorize(): bool { return true; } // التفويض في الـ Controller عبر Policy
    public function rules(): array {
        $id = $this->route('user')?->id;
        return [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['nullable', 'string', 'max:60', 'alpha_dash', Rule::unique('users', 'username')->ignore($id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($id)],
            'password' => [$id ? 'nullable' : 'required', 'confirmed', Password::min(8)],
            'is_active' => ['boolean'],
        ];
    }
    protected function prepareForValidation(): void { $this->merge(['is_active' => $this->boolean('is_active')]); }
}
