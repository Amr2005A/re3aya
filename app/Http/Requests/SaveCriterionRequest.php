<?php
namespace App\Http\Requests;
use App\Services\SettingsService;
use Illuminate\Foundation\Http\FormRequest;
class SaveCriterionRequest extends FormRequest {
    public function authorize(): bool { return true; }
    public function rules(): array {
        $min = SettingsService::allowNegativePoints() ? -SettingsService::maxPointsPerAward() : 0;
        return ['name' => ['required', 'string', 'max:100'], 'default_points' => ['required', 'integer', "min:$min", 'max:' . SettingsService::maxPointsPerAward()],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:65000']];
    }
}
