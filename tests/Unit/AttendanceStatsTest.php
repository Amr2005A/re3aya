<?php
namespace Tests\Unit;
use App\Services\AttendanceStats;
use PHPUnit\Framework\TestCase;
class AttendanceStatsTest extends TestCase {
    public function test_rate_excludes_excused_from_denominator(): void { $this->assertSame(80.0, AttendanceStats::rate(3, 1, 1, 5)); }
    public function test_rate_is_null_without_countable_days(): void { $this->assertNull(AttendanceStats::rate(0, 0, 0, 4)); }
}
