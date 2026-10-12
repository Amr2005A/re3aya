<?php
namespace App\Services;
class PhoneService {
    /** رابط WhatsApp أو null إن تعذّر تحديد الرقم الدولي */
    public static function whatsappUrl(?string $phone): ?string {
        $d = preg_replace('/\D+/', '', (string) $phone);
        if ($d === '') return null;
        if (str_starts_with($d, '00')) $d = substr($d, 2);
        elseif (str_starts_with($d, '0')) {
            $cc = preg_replace('/\D+/', '', (string) config('riaya.country_code'));
            if ($cc === '') return null;
            $d = $cc . ltrim($d, '0');
        }
        return strlen($d) >= 8 ? "https://wa.me/$d" : null;
    }
}
