<?php
namespace App\Enums;
enum PointTransactionType: string {
    case Award = 'award'; case Badge = 'badge'; case Reversal = 'reversal';
    public function label(): string { return match ($this) { self::Award => 'منح', self::Badge => 'وسام', self::Reversal => 'عكس' }; }
}
