<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Slugify\Contracts;

/**
 * Slugify Interface - PHP 8.4
 *
 * @package ArPHP\Core\Modules\Slugify
 */
interface SlugifyInterface
{
    /**
     * Generate URL-safe slug from Arabic text
     */
    public function slugify(string $text, string $separator = '-'): string;

    /**
     * Generate slug preserving Arabic characters
     */
    public function slugifyArabic(string $text, string $separator = '-'): string;

    /**
     * Transliterate Arabic to Latin for slug
     */
    public function transliterate(string $text): string;

    /**
     * Generate unique slug with suffix
     */
    public function uniqueSlug(string $text, callable $existsChecker): string;

    /**
     * Reverse transliterate slug back to Arabic (approximate)
     */
    public function reverseTransliterate(string $slug): string;
}
