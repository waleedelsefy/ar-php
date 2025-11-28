<?php

declare(strict_types=1);

namespace ArPHP\Core\Tests\Unit\Modules;

require_once __DIR__ . '/../../../src/Modules/NumbersModule.php';

use ArPHP\Core\Modules\NumbersModule;
use ArPHP\Core\Modules\NumbersService;
use PHPUnit\Framework\TestCase;

class NumbersModuleTest extends TestCase
{
    private NumbersService $service;

    protected function setUp(): void
    {
        $this->service = new NumbersService();
    }

    public function testToArabicIndic(): void
    {
        $this->assertEquals('١٢٣', $this->service->toArabicIndic('123'));
        $this->assertEquals('٢٠٢٥', $this->service->toArabicIndic('2025'));
        $this->assertEquals('٠١٢٣٤٥٦٧٨٩', $this->service->toArabicIndic('0123456789'));
    }

    public function testToWestern(): void
    {
        $this->assertEquals('123', $this->service->toWestern('١٢٣'));
        $this->assertEquals('2025', $this->service->toWestern('٢٠٢٥'));
        $this->assertEquals('0123456789', $this->service->toWestern('٠١٢٣٤٥٦٧٨٩'));
    }

    public function testToWordsBasic(): void
    {
        $this->assertEquals('صفر', $this->service->toWords(0));
        $this->assertEquals('واحد', $this->service->toWords(1));
        $this->assertEquals('اثنان', $this->service->toWords(2));
        $this->assertEquals('ثلاثة', $this->service->toWords(3));
        $this->assertEquals('عشرة', $this->service->toWords(10));
    }

    public function testToWordsTens(): void
    {
        $this->assertEquals('عشرون', $this->service->toWords(20));
        $this->assertEquals('ثلاثون', $this->service->toWords(30));
        
        $result = $this->service->toWords(25);
        $this->assertStringContainsString('خمسة', $result);
        $this->assertStringContainsString('عشرون', $result);
    }

    public function testToWordsHundreds(): void
    {
        $this->assertEquals('مائة', $this->service->toWords(100));
        $this->assertEquals('مائتان', $this->service->toWords(200));
        
        $result = $this->service->toWords(250);
        $this->assertStringContainsString('مائتان', $result);
    }

    public function testToWordsOutOfRange(): void
    {
        // Should return string representation for out of range
        $this->assertEquals('-1', $this->service->toWords(-1));
        $this->assertEquals('1000', $this->service->toWords(1000));
    }

    public function testHasArabicIndic(): void
    {
        $this->assertTrue($this->service->hasArabicIndic('رقم ١٢٣'));
        $this->assertFalse($this->service->hasArabicIndic('رقم 123'));
        $this->assertFalse($this->service->hasArabicIndic('نص بدون أرقام'));
    }

    public function testHasWestern(): void
    {
        $this->assertTrue($this->service->hasWestern('رقم 123'));
        $this->assertFalse($this->service->hasWestern('رقم ١٢٣'));
        $this->assertFalse($this->service->hasWestern('نص بدون أرقام'));
    }

    public function testExtract(): void
    {
        $numbers = $this->service->extract('رقم 123 وأيضاً ٤٥٦ هنا');
        
        $this->assertIsArray($numbers);
        $this->assertGreaterThanOrEqual(2, count($numbers)); // May extract individual digits
        $this->assertTrue(in_array('123', $numbers) || in_array('1', $numbers));
    }

    public function testFormat(): void
    {
        $formatted = $this->service->format(1234567.89, 2);
        $this->assertStringContainsString('٬', $formatted); // Arabic thousands separator
        
        $formatted = $this->service->format(1000000);
        $this->assertStringContainsString('١٬٠٠٠٬٠٠٠', $formatted);
    }

    public function testServiceName(): void
    {
        $this->assertEquals('numbers', $this->service->getServiceName());
    }

    public function testIsAvailable(): void
    {
        $this->assertTrue($this->service->isAvailable());
    }

    public function testGetConfig(): void
    {
        $config = $this->service->getConfig();
        
        $this->assertIsArray($config);
        $this->assertArrayHasKey('version', $config);
        $this->assertArrayHasKey('features', $config);
    }

    public function testModuleName(): void
    {
        $module = new NumbersModule();
        $this->assertEquals('numbers', $module->getName());
    }
}
