<?php
namespace App\Services;
use App\Models\Setting;
class SettingsService {
    public static function get(string $key, mixed $default = null): mixed { $s = Setting::find($key); return $s && $s->value !== null ? $s->value : $default; }
    public static function put(string $key, mixed $value, string $group = 'general'): void { Setting::updateOrCreate(['key' => $key], ['group' => $group, 'value' => $value]); }
    public static function allowNegativePoints(): bool { return (bool) self::get('points.allow_negative', false); }
    public static function maxPointsPerAward(): int { return max(1, (int) self::get('points.max_per_award', 100)); }
}
