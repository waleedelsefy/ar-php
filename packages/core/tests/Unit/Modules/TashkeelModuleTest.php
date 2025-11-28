<?php

declare(strict_types=1);

namespace ArPHP\Core\Tests\Unit\Modules;

require_once __DIR__ . '/../../../src/Modules/TashkeelModule.php';

use ArPHP\Core\Modules\TashkeelModule;
use ArPHP\Core\Modules\TashkeelService;
use PHPUnit\Framework\TestCase;

class TashkeelModuleTest extends TestCase
{
    private TashkeelService $service;

    protected function setUp(): void
    {
        $this->service = new TashkeelService();
    }

    public function testRemoveTashkeel(): void
    {
        $input = 'بِسْمِ اللَّهِ الرَّحْمَٰنِ';
        $expected = 'بسم الله الرحمٰن';
        
        $this->assertEquals($expected, $this->service->remove($input));
    }

    public function testHasTashkeel(): void
    {
        $withTashkeel = 'مَرْحَبًا';
        $withoutTashkeel = 'مرحبا';

        $this->assertTrue($this->service->has($withTashkeel));
        $this->assertFalse($this->service->has($withoutTashkeel));
    }

    public function testCountTashkeel(): void
    {
        $text = 'مَرْحَبًا'; // Has 4 tashkeel marks
        $this->assertGreaterThan(0, $this->service->count($text));
    }

    public function testExtractTashkeel(): void
    {
        $text = 'مَرْحَبًا';
        $marks = $this->service->extract($text);
        
        $this->assertIsArray($marks);
        $this->assertNotEmpty($marks);
    }

    public function testNormalize(): void
    {
        $this->assertEquals('احمد', $this->service->normalize('أحمد'));
        $this->assertEquals('ابراهيم', $this->service->normalize('إبراهيم'));
        $this->assertEquals('مدرسه', $this->service->normalize('مدرسة'));
        $this->assertEquals('يحيي', $this->service->normalize('يحيى'));
    }

    public function testAddTashkeel(): void
    {
        $this->assertEquals('مَرْحَبًا', $this->service->add('مرحبا'));
        $this->assertEquals('شُكْرًا', $this->service->add('شكرا'));
        $this->assertEquals('اللَّه', $this->service->add('الله'));
    }

    public function testServiceName(): void
    {
        $this->assertEquals('tashkeel', $this->service->getServiceName());
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
        $module = new TashkeelModule();
        $this->assertEquals('tashkeel', $module->getName());
    }
}
