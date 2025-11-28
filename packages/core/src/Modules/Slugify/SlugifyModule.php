<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Slugify;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Modules\Slugify\Contracts\SlugifyInterface;
use ArPHP\Core\Modules\Slugify\Services\SlugifyService;

/**
 * Slugify Module - PHP 8.4
 *
 * @package ArPHP\Core\Modules\Slugify
 */
final class SlugifyModule extends AbstractModule
{
    protected string $version = '1.0.0';

    /** @var array<string> */
    protected array $dependencies = [];

    private ?SlugifyService $service = null;

    public function __construct()
    {
    }

    public function getName(): string
    {
        return 'slugify';
    }

    public function register(): void
    {
        $this->service = new SlugifyService();
    }

    public function boot(): void
    {
        // Module ready
    }

    public function getService(): SlugifyInterface
    {
        if ($this->service === null) {
            $this->register();
        }

        return $this->service;
    }

    /**
     * Generate slug from text
     */
    public function slugify(string $text, string $separator = '-'): string
    {
        return $this->getService()->slugify($text, $separator);
    }

    /**
     * Generate Arabic slug
     */
    public function slugifyArabic(string $text, string $separator = '-'): string
    {
        return $this->getService()->slugifyArabic($text, $separator);
    }

    /**
     * Transliterate Arabic to Latin
     */
    public function transliterate(string $text): string
    {
        return $this->getService()->transliterate($text);
    }

    /**
     * Generate unique slug
     */
    public function uniqueSlug(string $text, callable $existsChecker): string
    {
        return $this->getService()->uniqueSlug($text, $existsChecker);
    }

    /**
     * Reverse transliterate
     */
    public function reverseTransliterate(string $slug): string
    {
        return $this->getService()->reverseTransliterate($slug);
    }

    public static function getIdentifier(): string
    {
        return 'slugify';
    }

    /**
     * @return array<string>
     */
    public static function getPublicApi(): array
    {
        return [
            'slugify',
            'slugifyArabic',
            'transliterate',
            'uniqueSlug',
            'reverseTransliterate',
        ];
    }
}
