<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class SaveAttendanceRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return ['date' => ['required', 'date', 'before_or_equal:today'], 'rows' => ['required', 'array'],
            'rows.*.status' => ['nullable', 'in:present,absent,late,excused'], 'rows.*.note' => ['nullable', 'string', 'max:255']];
    }
    public function messages(): array { return ['date.before_or_equal' => 'لا يمكن تسجيل حضور لتاريخ مستقبلي.', 'rows.required' => 'لا توجد بيانات للحفظ.']; }
}
