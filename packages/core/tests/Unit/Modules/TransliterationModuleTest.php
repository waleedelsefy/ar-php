<?php

declare(strict_types=1);

namespace ArPHP\Core\Tests\Unit\Modules;

require_once __DIR__ . '/../../../src/Modules/TransliterationModule.php';

use ArPHP\Core\Modules\TransliterationModule;
use ArPHP\Core\Modules\TransliterationService;
use PHPUnit\Framework\TestCase;

class TransliterationModuleTest extends TestCase
{
    private TransliterationService $service;

    protected function setUp(): void
    {
        $this->service = new TransliterationService();
    }

    public function testToLatin(): void
    {
        $this->assertEquals('mhmd', $this->service->toLatin('محمد'));
        $this->assertEquals('ahmd', $this->service->toLatin('أحمد'));
        $this->assertEquals('fatmh', $this->service->toLatin('فاطمة'));
    }

    public function testToArabic(): void
    {
        $result = $this->service->toArabic('ahmad');
        $this->assertStringContainsString('ا', $result);
        
        $result = $this->service->toArabic('khalid');
        $this->assertStringContainsString('خ', $result);
    }

    public function testIsArabic(): void
    {
        $this->assertTrue($this->service->isArabic('محمد'));
        $this->assertTrue($this->service->isArabic('نص عربي'));
        $this->assertFalse($this->service->isArabic('Ahmad'));
        $this->assertFalse($this->service->isArabic('123'));
    }

    public function testIsLatin(): void
    {
        $this->assertTrue($this->service->isLatin('Ahmad'));
        $this->assertTrue($this->service->isLatin('test text'));
        $this->assertFalse($this->service->isLatin('محمد'));
        $this->assertFalse($this->service->isLatin('test123'));
    }

    public function testConvert(): void
    {
        // Arabic to Latin
        $result = $this->service->convert('محمد');
        $this->assertEquals('mhmd', $result);

        // Latin to Arabic
        $result = $this->service->convert('ahmad');
        $this->assertStringContainsString('ا', $result);

        // Non-convertible text returns as-is
        $result = $this->service->convert('123');
        $this->assertEquals('123', $result);
    }

    public function testServiceName(): void
    {
        $this->assertEquals('transliteration', $this->service->getServiceName());
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
        $this->assertArrayHasKey('standards', $config);
        $this->assertArrayHasKey('features', $config);
        $this->assertIsArray($config['standards']);
        $this->assertContains('ALA-LC', $config['standards']);
    }

    public function testModuleName(): void
    {
        $module = new TransliterationModule();
        $this->assertEquals('transliteration', $module->getName());
    }
}
