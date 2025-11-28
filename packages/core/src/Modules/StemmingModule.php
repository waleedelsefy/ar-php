<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Arabic;
use ArPHP\Core\Contracts\ServiceInterface;

/**
 * Stemming Service - Arabic Root Extraction
 * 
 * Extracts roots from Arabic words (light stemmer)
 */
class StemmingService implements ServiceInterface
{
    /**
     * Common Arabic prefixes
     * 
     * @var array<int, string>
     */
    private const PREFIXES = [
        'ال',   // The
        'و',    // And
        'ف',    // So/Then
        'ب',    // By/With
        'ك',    // Like
        'ل',    // To/For
        'لل',   // To the
        'بال',  // By the
        'كال',  // Like the
        'فال',  // So the
        'وال',  // And the
    ];

    /**
     * Common Arabic suffixes
     * 
     * @var array<int, string>
     */
    private const SUFFIXES = [
        'ها',   // Her/Its
        'ان',   // Dual
        'ات',   // Feminine plural
        'ون',   // Masculine plural
        'ين',   // Masculine plural (genitive)
        'ه',    // Him/It
        'ة',    // Feminine marker
        'ي',    // My
        'ك',    // Your
        'كم',   // Your (plural)
        'كما',  // Your (dual)
        'كن',   // Your (feminine plural)
        'نا',   // Our/Us
        'ني',   // Me
        'وا',   // They (plural)
        'ما',   // What/Dual
        'هم',   // Them
        'هما',  // Them (dual)
        'هن',   // Them (feminine)
    ];

    /**
     * Extract root from Arabic word
     * 
     * This is a light stemmer. For production, consider using
     * dedicated Arabic NLP libraries.
     */
    public function stem(string $word): string
    {
        // Normalize the word first
        $word = $this->normalize($word);
        
        // Remove prefixes
        $word = $this->removePrefix($word);
        
        // Remove suffixes
        $word = $this->removeSuffix($word);
        
        // Remove long vowels and diacritics
        $word = $this->removeLongVowels($word);
        
        return $word;
    }

    /**
     * Stem multiple words
     * 
     * @param array<int, string> $words
     * @return array<int, string>
     */
    public function stemBatch(array $words): array
    {
        return array_map(fn($word) => $this->stem($word), $words);
    }

    /**
     * Extract roots from text
     * 
     * @return array<int, string>
     */
    public function extractRoots(string $text): array
    {
        $words = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        if ($words === false) {
            return [];
        }
        
        $roots = [];
        foreach ($words as $word) {
            $root = $this->stem($word);
            if (!in_array($root, $roots, true)) {
                $roots[] = $root;
            }
        }
        
        return $roots;
    }

    /**
     * Normalize word before stemming
     */
    private function normalize(string $word): string
    {
        // Remove tashkeel
        $word = preg_replace('/[\x{064B}-\x{065F}]/u', '', $word) ?? $word;
        
        // Normalize Alef
        $word = str_replace(['أ', 'إ', 'آ', 'ٱ'], 'ا', $word);
        
        // Normalize Yeh
        $word = str_replace(['ى', 'ئ'], 'ي', $word);
        
        // Normalize Teh Marbuta
        $word = str_replace('ة', 'ه', $word);
        
        return $word;
    }

    /**
     * Remove prefix from word
     */
    private function removePrefix(string $word): string
    {
        foreach (self::PREFIXES as $prefix) {
            if (mb_strlen($word, 'UTF-8') > mb_strlen($prefix, 'UTF-8') + 2) {
                if (mb_substr($word, 0, mb_strlen($prefix, 'UTF-8'), 'UTF-8') === $prefix) {
                    return mb_substr($word, mb_strlen($prefix, 'UTF-8'), null, 'UTF-8');
                }
            }
        }
        
        return $word;
    }

    /**
     * Remove suffix from word
     */
    private function removeSuffix(string $word): string
    {
        foreach (self::SUFFIXES as $suffix) {
            $suffixLen = mb_strlen($suffix, 'UTF-8');
            $wordLen = mb_strlen($word, 'UTF-8');
            
            if ($wordLen > $suffixLen + 2) {
                if (mb_substr($word, -$suffixLen, null, 'UTF-8') === $suffix) {
                    return mb_substr($word, 0, $wordLen - $suffixLen, 'UTF-8');
                }
            }
        }
        
        return $word;
    }

    /**
     * Remove long vowels
     */
    private function removeLongVowels(string $word): string
    {
        // Remove Alef, Waw, Yeh when used as long vowels
        // Keep at least 3 characters
        if (mb_strlen($word, 'UTF-8') <= 3) {
            return $word;
        }
        
        $word = str_replace(['ا', 'و', 'ي'], '', $word);
        
        // If too short after removal, return original
        if (mb_strlen($word, 'UTF-8') < 3) {
            return $word;
        }
        
        return $word;
    }

    public function getServiceName(): string
    {
        return 'stemming';
    }

    public function getConfig(): array
    {
        return [
            'version' => '1.0.0',
            'type' => 'light-stemmer',
            'features' => ['stem', 'stemBatch', 'extractRoots'],
        ];
    }

    public function isAvailable(): bool
    {
        return extension_loaded('mbstring');
    }
}

/**
 * Stemming Module
 * 
 * Registers the Stemming service for Arabic root extraction
 */
class StemmingModule extends AbstractModule
{
    protected string $version = '1.0.0';

    public function getName(): string
    {
        return 'stemming';
    }

    public function register(): void
    {
        Arabic::container()->register('stemming', function () {
            return new StemmingService();
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
