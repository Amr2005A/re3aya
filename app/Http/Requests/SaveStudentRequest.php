<?php
namespace App\Http\Requests;
use App\Enums\StudentStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class SaveStudentRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        $phone = ['string', 'max:30', 'regex:/^[0-9+\s\-()]{6,}$/'];
        return [
            'name' => ['required', 'string', 'max:255'], 'father_name' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string', 'max:255'], 'father_job' => ['nullable', 'string', 'max:255'],
            'phone' => ['required', ...$phone], 'phone_extra' => ['nullable', ...$phone],
            'grade_level_id' => ['nullable', Rule::exists('grade_levels', 'id')],
            'section_id' => ['nullable', Rule::exists('sections', 'id')],
            'supervisor_id' => ['nullable', Rule::exists('supervisors', 'id')->where('is_active', true)],
            'status' => ['required', Rule::enum(StudentStatus::class)],
            'registered_at' => ['nullable', 'date'],
        ];
    }
    public function messages(): array { return ['phone.regex' => 'صيغة رقم الهاتف غير صحيحة.', 'phone.required' => 'رقم التواصل الأساسي إجباري.']; }
}
