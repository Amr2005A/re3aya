<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class SaveCircleRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return ['name' => ['required', 'string', 'max:100'], 'description' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65000'], 'is_active' => ['boolean']];
    }
    protected function prepareForValidation(): void { $this->merge(['is_active' => $this->boolean('is_active')]); }
}
