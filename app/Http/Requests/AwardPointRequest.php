<?php
namespace App\Http\Requests;
use App\Models\CircleCriterion;
use App\Services\SettingsService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
class AwardPointRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        return [
            'student_id' => ['required', 'integer', 'exists:students,id'],
            'circle_id' => ['nullable', Rule::exists('circles', 'id')->where('is_active', true)->whereNull('deleted_at')],
            'criteria_id' => ['nullable', Rule::exists('circle_criteria', 'id')->where('is_active', true)->whereNull('deleted_at')],
            'points' => ['required', 'integer'],
            'reason' => ['required_without:criteria_id', 'nullable', 'string', 'max:255'],
            'note' => ['nullable', 'string', 'max:500'],
        ];
    }
    public function withValidator($v): void {
        $v->after(function ($v) {
            if ($v->errors()->has('points')) return;
            $p = (int) $this->input('points'); $max = SettingsService::maxPointsPerAward();
            if ($p === 0) $v->errors()->add('points', 'عدد النقاط لا يمكن أن يكون صفراً.');
            elseif ($p < 0 && ! SettingsService::allowNegativePoints()) $v->errors()->add('points', 'خصم النقاط غير مفعّل. استخدم «عكس قيد» لتصحيح خطأ.');
            elseif (abs($p) > $max) $v->errors()->add('points', "الحد الأقصى للعملية الواحدة $max نقطة.");
            if ($this->filled('criteria_id') && $this->filled('circle_id')) {
                $c = CircleCriterion::find($this->criteria_id);
                if ($c && (int) $c->circle_id !== (int) $this->circle_id) $v->errors()->add('criteria_id', 'البند لا يتبع هذه الحلقة.');
            }
        });
    }
    public function messages(): array { return ['reason.required_without' => 'اكتب سبب النقاط أو اختر بنداً.', 'points.integer' => 'عدد النقاط يجب أن يكون رقماً صحيحاً.']; }
}
