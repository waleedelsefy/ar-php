<?php

declare(strict_types=1);

namespace ArPHP\Core\Tests\Unit\Modules;

require_once __DIR__ . '/../../../src/Modules/TextCleanerModule.php';

use ArPHP\Core\Modules\TextCleanerModule;
use ArPHP\Core\Modules\TextCleanerService;
use PHPUnit\Framework\TestCase;

class TextCleanerModuleTest extends TestCase
{
    private TextCleanerService $service;

    protected function setUp(): void
    {
        $this->service = new TextCleanerService();
    }

    public function testRemoveExtraSpaces(): void
    {
        $input = '  هذا   نص   مع    مسافات   كثيرة  ';
        $expected = 'هذا نص مع مسافات كثيرة';
        
        $this->assertEquals($expected, $this->service->removeExtraSpaces($input));
    }

    public function testRemoveHtml(): void
    {
        $input = '<p>نص <strong>عربي</strong> مع HTML</p>';
        $expected = 'نص عربي مع HTML';
        
        $this->assertEquals($expected, $this->service->removeHtml($input));
    }

    public function testRemoveUrls(): void
    {
        $input = 'زيارة https://example.com للمزيد';
        $result = $this->service->removeUrls($input);
        
        $this->assertStringNotContainsString('https://', $result);
        $this->assertStringContainsString('زيارة', $result);
    }

    public function testRemoveEmails(): void
    {
        $input = 'تواصل معنا test@example.com للمساعدة';
        $result = $this->service->removeEmails($input);
        
        $this->assertStringNotContainsString('test@example.com', $result);
        $this->assertStringContainsString('تواصل', $result);
    }

    public function testRemoveEnglish(): void
    {
        $input = 'النص العربي مع English text';
        $result = $this->service->removeEnglish($input);
        
        $this->assertStringNotContainsString('English', $result);
        $this->assertStringNotContainsString('text', $result);
        $this->assertStringContainsString('النص', $result);
    }

    public function testRemoveNumbers(): void
    {
        $input = 'النص 123 مع أرقام ٤٥٦';
        $result = $this->service->removeNumbers($input);
        
        $this->assertStringNotContainsString('123', $result);
        $this->assertStringNotContainsString('٤٥٦', $result);
        $this->assertStringContainsString('النص', $result);
    }

    public function testRemovePunctuation(): void
    {
        $input = 'نص، مع! علامات؟ ترقيم.';
        $result = $this->service->removePunctuation($input);
        
        $this->assertStringNotContainsString('،', $result);
        $this->assertStringNotContainsString('!', $result);
        $this->assertStringNotContainsString('؟', $result);
        $this->assertStringNotContainsString('.', $result);
    }

    public function testKeepArabicOnly(): void
    {
        $input = 'النص العربي 123 مع English';
        $result = $this->service->keepArabicOnly($input);
        
        $this->assertStringNotContainsString('123', $result);
        $this->assertStringNotContainsString('English', $result);
        $this->assertStringContainsString('النص', $result);
    }

    public function testCleanWithDefaults(): void
    {
        $input = '<p>  نص  عربي  https://test.com  </p>';
        $result = $this->service->clean($input);
        
        $this->assertStringNotContainsString('<p>', $result);
        $this->assertStringNotContainsString('https://', $result);
        $this->assertStringContainsString('نص', $result);
    }

    public function testCleanWithCustomOptions(): void
    {
        $input = 'النص العربي 123 مع English';
        $result = $this->service->clean($input, [
            'numbers' => true,
            'english' => true,
        ]);
        
        $this->assertStringNotContainsString('123', $result);
        $this->assertStringNotContainsString('English', $result);
        $this->assertStringContainsString('النص', $result);
    }

    public function testCountWords(): void
    {
        $text = 'هذا نص عربي للاختبار';
        $count = $this->service->countWords($text);
        
        $this->assertEquals(4, $count);
    }

    public function testCountWordsWithExtraSpaces(): void
    {
        $text = '  هذا   نص   عربي  ';
        $count = $this->service->countWords($text);
        
        $this->assertEquals(3, $count);
    }

    public function testCountChars(): void
    {
        $text = 'هذا نص';
        $count = $this->service->countChars($text);
        
        $this->assertGreaterThan(0, $count);
    }

    public function testCountCharsIgnoresSpaces(): void
    {
        $text1 = 'هذا';
        $text2 = 'ه ذ ا';
        
        $this->assertEquals(
            $this->service->countChars($text1),
            $this->service->countChars($text2)
        );
    }

    public function testServiceName(): void
    {
        $this->assertEquals('text-cleaner', $this->service->getServiceName());
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
        $this->assertIsArray($config['features']);
    }

    public function testModuleName(): void
    {
        $module = new TextCleanerModule();
        $this->assertEquals('text-cleaner', $module->getName());
    }
}
