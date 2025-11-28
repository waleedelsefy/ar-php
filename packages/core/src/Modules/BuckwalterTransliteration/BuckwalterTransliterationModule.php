<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\BuckwalterTransliteration;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Modules\BuckwalterTransliteration\Contracts\BuckwalterTransliterationInterface;
use ArPHP\Core\Modules\BuckwalterTransliteration\Services\BuckwalterTransliterationService;

/**
 * Buckwalter Transliteration Module - PHP 8.4
 *
 * Provides Arabic-Latin transliteration using various standards.
 *
 * @package ArPHP\Core\Modules\BuckwalterTransliteration
 */
final class BuckwalterTransliterationModule extends AbstractModule
{
    protected string $version = '1.0.0';

    /** @var array<string> */
    protected array $dependencies = [];

    private ?BuckwalterTransliterationService $service = null;

    public function __construct()
    {
    }

    public function getName(): string
    {
        return 'buckwalter_transliteration';
    }

    public function register(): void
    {
        $this->service = new BuckwalterTransliterationService();
    }

    public function boot(): void
    {
        // Module ready
    }

    public function getService(): BuckwalterTransliterationInterface
    {
        if ($this->service === null) {
            $this->register();
        }

        return $this->service;
    }

    /**
     * Arabic to Buckwalter
     */
    public function toLatinBuckwalter(string $text): string
    {
        return $this->getService()->toLatinBuckwalter($text);
    }

    /**
     * Buckwalter to Arabic
     */
    public function toArabicBuckwalter(string $text): string
    {
        return $this->getService()->toArabicBuckwalter($text);
    }

    /**
     * Arabic to Safe Buckwalter
     */
    public function toLatinSafeBuckwalter(string $text): string
    {
        return $this->getService()->toLatinSafeBuckwalter($text);
    }

    /**
     * Safe Buckwalter to Arabic
     */
    public function fromSafeBuckwalter(string $text): string
    {
        return $this->getService()->fromSafeBuckwalter($text);
    }

    /**
     * Arabic to ISO 233
     */
    public function toLatinIso233(string $text): string
    {
        return $this->getService()->toLatinIso233($text);
    }

    /**
     * Arabic to DIN 31635
     */
    public function toLatinDin31635(string $text): string
    {
        return $this->getService()->toLatinDin31635($text);
    }

    /**
     * Arabic to Library of Congress
     */
    public function toLatinLoc(string $text): string
    {
        return $this->getService()->toLatinLoc($text);
    }

    /**
     * Arabic to phonetic
     */
    public function toPhonetic(string $text): string
    {
        return $this->getService()->toPhonetic($text);
    }

    /**
     * Latin to Arabic using specified scheme
     */
    public function toArabic(string $text, string $scheme): string
    {
        return $this->getService()->toArabic($text, $scheme);
    }

    /**
     * Get available schemes
     *
     * @return array<string>
     */
    public function getSchemes(): array
    {
        return $this->getService()->getSchemes();
    }

    public static function getIdentifier(): string
    {
        return 'buckwalter_transliteration';
    }

    /**
     * @return array<string>
     */
    public static function getPublicApi(): array
    {
        return [
            'toLatinBuckwalter',
            'toArabicBuckwalter',
            'toLatinSafeBuckwalter',
            'fromSafeBuckwalter',
            'toLatinIso233',
            'toLatinDin31635',
            'toLatinLoc',
            'toPhonetic',
            'toArabic',
            'getSchemes',
        ];
    }
}
