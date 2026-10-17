<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class SaveNoteRequest extends FormRequest {
    public function authorize(): bool { return true; } // التفويض في الـ Controller عبر Policy
    public function rules(): array { return ['body' => ['required', 'string', 'min:2', 'max:5000']]; }
    public function messages(): array { return ['body.required' => 'اكتب نص الملاحظة.', 'body.min' => 'الملاحظة قصيرة جداً.', 'body.max' => 'الملاحظة طويلة (الحد 5000 حرف).']; }
}
