<?php
namespace Tests\Unit;
use App\Services\PhoneService;
use PHPUnit\Framework\TestCase;
class PhoneServiceTest extends TestCase {
    public function test_international_formats_are_normalized(): void {
        $this->assertSame('https://wa.me/963912345678', PhoneService::whatsappUrl('+963 912 345 678'));
        $this->assertSame('https://wa.me/963912345678', PhoneService::whatsappUrl('00963-912-345-678'));
    }
    public function test_empty_or_too_short_returns_null(): void { $this->assertNull(PhoneService::whatsappUrl('')); $this->assertNull(PhoneService::whatsappUrl('123')); }
}
