<?php
namespace App\Enums;
enum PointTransactionType: string {
    case Award = 'award';
    case Badge = 'badge';
    case Reversal = 'reversal';
}
