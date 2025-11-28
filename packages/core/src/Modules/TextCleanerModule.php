<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Arabic;
use ArPHP\Core\Contracts\ServiceInterface;

/**
 * Text Cleaner Service - Arabic Text Cleaning
 * 
 * Handles cleaning and normalizing Arabic text
 */
class TextCleanerService implements ServiceInterface
{
    /**
     * Remove extra whitespace from text
     */
    public function removeExtraSpaces(string $text): string
    {
        // Replace multiple spaces with single space
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;
        
        // Trim whitespace
        return trim($text);
    }

    /**
     * Remove HTML tags
     */
    public function removeHtml(string $text): string
    {
        return strip_tags($text);
    }

    /**
     * Remove URLs from text
     */
    public function removeUrls(string $text): string
    {
        $pattern = '#\bhttps?://[^\s()<>]+(?:\([\w\d]+\)|([^[:punct:]\s]|/))#';
        return preg_replace($pattern, '', $text) ?? $text;
    }

    /**
     * Remove email addresses
     */
    public function removeEmails(string $text): string
    {
        $pattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/';
        return preg_replace($pattern, '', $text) ?? $text;
    }

    /**
     * Remove English characters
     */
    public function removeEnglish(string $text): string
    {
        return preg_replace('/[a-zA-Z]+/', '', $text) ?? $text;
    }

    /**
     * Remove numbers (both Arabic and Western)
     */
    public function removeNumbers(string $text): string
    {
        $text = preg_replace('/[0-9]+/', '', $text) ?? $text;
        return preg_replace('/[٠-٩]+/u', '', $text) ?? $text;
    }

    /**
     * Remove punctuation
     */
    public function removePunctuation(string $text): string
    {
        $arabicPunctuation = '،؛؟.!:""\'\'()[]{}«»';
        $englishPunctuation = ',.;?!:\'"()[]{}';
        
        $pattern = '/[' . preg_quote($arabicPunctuation . $englishPunctuation, '/') . ']/u';
        return preg_replace($pattern, '', $text) ?? $text;
    }

    /**
     * Remove emojis
     */
    public function removeEmojis(string $text): string
    {
        // Remove emoji characters
        return preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $text) ?? $text;
    }

    /**
     * Keep only Arabic text
     */
    public function keepArabicOnly(string $text): string
    {
        return preg_replace('/[^\x{0600}-\x{06FF}\s]/u', '', $text) ?? $text;
    }

    /**
     * Clean text comprehensively
     * 
     * @param array<string, bool> $options Cleaning options
     */
    public function clean(string $text, array $options = []): string
    {
        $defaults = [
            'html' => true,
            'urls' => true,
            'emails' => true,
            'extra_spaces' => true,
            'english' => false,
            'numbers' => false,
            'punctuation' => false,
            'emojis' => true,
        ];

        $options = array_merge($defaults, $options);

        if ($options['html']) {
            $text = $this->removeHtml($text);
        }

        if ($options['urls']) {
            $text = $this->removeUrls($text);
        }

        if ($options['emails']) {
            $text = $this->removeEmails($text);
        }

        if ($options['emojis']) {
            $text = $this->removeEmojis($text);
        }

        if ($options['english']) {
            $text = $this->removeEnglish($text);
        }

        if ($options['numbers']) {
            $text = $this->removeNumbers($text);
        }

        if ($options['punctuation']) {
            $text = $this->removePunctuation($text);
        }

        if ($options['extra_spaces']) {
            $text = $this->removeExtraSpaces($text);
        }

        return $text;
    }

    /**
     * Count Arabic words in text
     */
    public function countWords(string $text): int
    {
        $text = $this->removeExtraSpaces($text);
        $words = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        return $words !== false ? count($words) : 0;
    }

    /**
     * Count Arabic characters (excluding spaces)
     */
    public function countChars(string $text): int
    {
        $arabicOnly = $this->keepArabicOnly($text);
        $arabicOnly = str_replace(' ', '', $arabicOnly);
        return mb_strlen($arabicOnly, 'UTF-8');
    }

    public function getServiceName(): string
    {
        return 'text-cleaner';
    }

    public function getConfig(): array
    {
        return [
            'version' => '1.0.0',
            'features' => [
                'removeExtraSpaces',
                'removeHtml',
                'removeUrls',
                'removeEmails',
                'removeEnglish',
                'removeNumbers',
                'removePunctuation',
                'removeEmojis',
                'keepArabicOnly',
                'clean',
                'countWords',
                'countChars',
            ],
        ];
    }

    public function isAvailable(): bool
    {
        return extension_loaded('mbstring');
    }
}

/**
 * Text Cleaner Module
 * 
 * Registers the Text Cleaner service for Arabic text cleaning
 */
class TextCleanerModule extends AbstractModule
{
    protected string $version = '1.0.0';

    public function getName(): string
    {
        return 'text-cleaner';
    }

    public function register(): void
    {
        Arabic::container()->register('text-cleaner', function () {
            return new TextCleanerService();
        });
    }

    public function boot(): void
    {
        // Module is ready
    }

    /**
     * @return array<int, string>
     */
    public function getDependencies(): array
    {
        return [];
    }
}
