<?php
namespace App\Enums;
enum StudentStatus: string {
    case Active = 'active'; case Suspended = 'suspended'; case Graduated = 'graduated'; case Withdrawn = 'withdrawn';
    public function label(): string {
        return match ($this) { self::Active => 'فعّال', self::Suspended => 'موقوف', self::Graduated => 'متخرّج', self::Withdrawn => 'منسحب' };
    }
    public function badgeClass(): string { return $this === self::Active ? 'badge-soft-ok' : 'badge-soft-off'; }
}
