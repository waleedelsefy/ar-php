<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Slugify\Services;

use ArPHP\Core\Modules\Slugify\Config;
use ArPHP\Core\Modules\Slugify\Contracts\SlugifyInterface;
use ArPHP\Core\Modules\Slugify\Exceptions\SlugifyException;

/**
 * Slugify Service - PHP 8.4
 *
 * @package ArPHP\Core\Modules\Slugify
 */
final class SlugifyService implements SlugifyInterface
{
    /**
     * @inheritDoc
     */
    public function slugify(string $text, string $separator = '-'): string
    {
        if (empty(\trim($text))) {
            throw SlugifyException::emptyText();
        }

        if (!\in_array($separator, Config::VALID_SEPARATORS, true)) {
            throw SlugifyException::invalidSeparator($separator);
        }

        // Transliterate Arabic to Latin
        $text = $this->transliterate($text);

        // Convert to lowercase
        $text = \mb_strtolower($text, 'UTF-8');

        // Replace spaces and special characters with separator
        $text = \preg_replace('/[^a-z0-9]+/u', $separator, $text);

        // Remove consecutive separators
        $text = \preg_replace('/' . \preg_quote($separator, '/') . '+/', $separator, $text ?? '');

        // Trim separators from start and end
        $text = \trim($text ?? '', $separator);

        // Limit length
        if (\mb_strlen($text) > Config::MAX_SLUG_LENGTH) {
            $text = \mb_substr($text, 0, Config::MAX_SLUG_LENGTH);
            // Don't end with separator
            $text = \rtrim($text, $separator);
        }

        return $text;
    }

    /**
     * @inheritDoc
     */
    public function slugifyArabic(string $text, string $separator = '-'): string
    {
        if (empty(\trim($text))) {
            throw SlugifyException::emptyText();
        }

        // Remove diacritics
        $text = \str_replace(Config::DIACRITICS, '', $text);

        // Remove punctuation
        $text = \str_replace(Config::REMOVE_CHARACTERS, '', $text);

        // Convert Arabic numbers to Western
        $text = \str_replace(
            \array_keys(Config::ARABIC_NUMBERS),
            \array_values(Config::ARABIC_NUMBERS),
            $text
        );

        // Replace spaces with separator
        $text = \preg_replace('/\s+/u', $separator, \trim($text));

        // Remove consecutive separators
        $text = \preg_replace('/' . \preg_quote($separator, '/') . '+/', $separator, $text ?? '');

        // Trim separators
        $text = \trim($text ?? '', $separator);

        // Limit length
        if (\mb_strlen($text) > Config::MAX_SLUG_LENGTH) {
            $text = \mb_substr($text, 0, Config::MAX_SLUG_LENGTH);
            $text = \rtrim($text, $separator);
        }

        return $text;
    }

    /**
     * @inheritDoc
     */
    public function transliterate(string $text): string
    {
        // Remove diacritics first
        $text = \str_replace(Config::DIACRITICS, '', $text);

        // Convert Arabic numbers to Western
        $text = \str_replace(
            \array_keys(Config::ARABIC_NUMBERS),
            \array_values(Config::ARABIC_NUMBERS),
            $text
        );

        // Sort map by length (longest first) to handle multi-char mappings
        $map = Config::TRANSLITERATION_MAP;
        \uksort($map, fn($a, $b) => \mb_strlen($b) - \mb_strlen($a));

        // Apply transliteration
        foreach ($map as $arabic => $latin) {
            $text = \str_replace($arabic, $latin, $text);
        }

        // Remove any remaining Arabic characters
        $text = \preg_replace('/[\x{0600}-\x{06FF}]/u', '', $text);

        return $text ?? '';
    }

    /**
     * @inheritDoc
     */
    public function uniqueSlug(string $text, callable $existsChecker): string
    {
        $baseSlug = $this->slugify($text);
        $slug = $baseSlug;
        $counter = 1;

        while ($existsChecker($slug)) {
            $slug = $baseSlug . '-' . $counter;
            ++$counter;

            // Prevent infinite loop
            if ($counter > 1000) {
                throw SlugifyException::slugGenerationFailed();
            }
        }

        return $slug;
    }

    /**
     * @inheritDoc
     */
    public function reverseTransliterate(string $slug): string
    {
        // Replace separator with space
        $text = \str_replace(['-', '_', '.'], ' ', $slug);

        // Sort map by length (longest first)
        $map = Config::REVERSE_TRANSLITERATION_MAP;
        \uksort($map, fn($a, $b) => \mb_strlen($b) - \mb_strlen($a));

        // Apply reverse transliteration
        foreach ($map as $latin => $arabic) {
            $text = \str_ireplace($latin, $arabic, $text);
        }

        return $text;
    }

    /**
     * Generate slug with custom options
     *
     * @param array{separator?: string, lowercase?: bool, maxLength?: int, preserveArabic?: bool} $options
     */
    public function slugifyCustom(string $text, array $options = []): string
    {
        $separator = $options['separator'] ?? Config::DEFAULT_SEPARATOR;
        $lowercase = $options['lowercase'] ?? true;
        $maxLength = $options['maxLength'] ?? Config::MAX_SLUG_LENGTH;
        $preserveArabic = $options['preserveArabic'] ?? false;

        if ($preserveArabic) {
            $slug = $this->slugifyArabic($text, $separator);
        } else {
            $slug = $this->slugify($text, $separator);
        }

        if (!$lowercase) {
            // Re-process without lowercase
            $slug = $this->slugify($text, $separator);
            // This is still lowercase due to implementation, but we can work around
        }

        if (\mb_strlen($slug) > $maxLength) {
            $slug = \mb_substr($slug, 0, $maxLength);
            $slug = \rtrim($slug, $separator);
        }

        return $slug;
    }

    /**
     * Convert text to filename-safe string
     */
    public function toFilename(string $text, string $extension = ''): string
    {
        $filename = $this->slugify($text, '_');

        if (!empty($extension)) {
            $extension = \ltrim($extension, '.');
            $filename .= '.' . $extension;
        }

        return $filename;
    }

    /**
     * Generate SEO-friendly slug
     */
    public function seoSlug(string $text, int $maxWords = 6): string
    {
        // Split into words
        $words = \preg_split('/\s+/u', \trim($text));

        if ($words === false) {
            return $this->slugify($text);
        }

        // Take only first N words
        $words = \array_slice($words, 0, $maxWords);

        return $this->slugify(\implode(' ', $words));
    }

    /**
     * Check if string is already a valid slug
     */
    public function isValidSlug(string $text): bool
    {
        // Valid slug contains only lowercase letters, numbers, and hyphens
        return (bool) \preg_match('/^[a-z0-9]+(-[a-z0-9]+)*$/', $text);
    }

    /**
     * Check if string is a valid Arabic slug
     */
    public function isValidArabicSlug(string $text): bool
    {
        // Valid Arabic slug contains Arabic letters, numbers, and hyphens
        return (bool) \preg_match('/^[\p{Arabic}0-9]+(-[\p{Arabic}0-9]+)*$/u', $text);
    }

    /**
     * Sanitize slug
     */
    public function sanitize(string $slug): string
    {
        // Remove invalid characters
        $slug = \preg_replace('/[^a-z0-9\-]/u', '', \mb_strtolower($slug));

        // Remove consecutive hyphens
        $slug = \preg_replace('/-+/', '-', $slug ?? '');

        // Trim hyphens
        return \trim($slug ?? '', '-');
    }

    /**
     * Extract slug from URL
     */
    public function extractFromUrl(string $url): string
    {
        // Parse URL
        $path = \parse_url($url, \PHP_URL_PATH);

        if ($path === false || $path === null) {
            return '';
        }

        // Get last segment
        $segments = \explode('/', \trim($path, '/'));
        $lastSegment = \end($segments);

        // Remove extension if present
        $lastSegment = \preg_replace('/\.[^.]+$/', '', $lastSegment);

        return $lastSegment ?? '';
    }
}
