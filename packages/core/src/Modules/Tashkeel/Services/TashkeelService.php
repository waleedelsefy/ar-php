<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Tashkeel\Services;

use ArPHP\Core\Modules\Tashkeel\Contracts\TashkeelInterface;
use ArPHP\Core\Modules\Tashkeel\Config;

/**
 * Tashkeel Service - PHP 8.4
 *
 * Arabic diacritization operations.
 *
 * @package ArPHP\Core\Modules\Tashkeel
 */
final class TashkeelService implements TashkeelInterface
{
    /**
     * Constructor
     */
    public function __construct() {}

    /**
     * {@inheritdoc}
     */
    public function removeTashkeel(string $text): string
    {
        $pattern = '[' . implode('', Config::ALL_DIACRITICS) . ']';
        return preg_replace("/{$pattern}/u", '', $text) ?? $text;
    }

    /**
     * {@inheritdoc}
     */
    public function removeDiacritic(string $text, string $diacritic): string
    {
        return str_replace($diacritic, '', $text);
    }

    /**
     * {@inheritdoc}
     */
    public function hasTashkeel(string $text): bool
    {
        foreach (Config::ALL_DIACRITICS as $diacritic) {
            if (mb_strpos($text, $diacritic) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function countTashkeel(string $text): int
    {
        $count = 0;
        foreach (Config::ALL_DIACRITICS as $diacritic) {
            $count += mb_substr_count($text, $diacritic);
        }
        return $count;
    }

    /**
     * {@inheritdoc}
     */
    public function getDiacriticStats(string $text): array
    {
        $stats = [];

        foreach (Config::ALL_DIACRITICS as $diacritic) {
            $count = mb_substr_count($text, $diacritic);
            if ($count > 0) {
                $name = Config::DIACRITIC_NAMES[$diacritic] ?? 'unknown';
                $stats[$name] = $count;
            }
        }

        return $stats;
    }

    /**
     * {@inheritdoc}
     */
    public function extractTashkeel(string $text): array
    {
        $diacritics = [];
        $length = mb_strlen($text);

        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($text, $i, 1);
            if (in_array($char, Config::ALL_DIACRITICS, true)) {
                $diacritics[] = $char;
            }
        }

        return $diacritics;
    }

    /**
     * {@inheritdoc}
     */
    public function addSukoon(string $text): string
    {
        $result = '';
        $chars = $this->mbStringToArray($text);
        $count = count($chars);

        for ($i = 0; $i < $count; $i++) {
            $char = $chars[$i];
            $result .= $char;

            // Check if current char is an Arabic letter
            if (in_array($char, Config::ARABIC_LETTERS, true)) {
                // Check if next char is NOT a diacritic
                $hasHaraka = false;
                if ($i + 1 < $count) {
                    $nextChar = $chars[$i + 1];
                    if (in_array($nextChar, Config::ALL_DIACRITICS, true)) {
                        $hasHaraka = true;
                    }
                }

                // Add sukoon if no haraka follows (and not end of word)
                if (!$hasHaraka && $i + 1 < $count) {
                    $nextChar = $chars[$i + 1] ?? '';
                    // Don't add sukoon before space or punctuation
                    if (in_array($nextChar, Config::ARABIC_LETTERS, true)) {
                        // Don't add sukoon, letter flows to next
                    } elseif ($nextChar !== ' ' && $nextChar !== "\n") {
                        $result .= Config::SUKOON;
                    }
                }
            }
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function normalizeShadda(string $text): string
    {
        // Normalize shadda + vowel combinations (reorder if needed)
        // Standard order: letter + shadda + vowel

        // Pattern: vowel before shadda -> shadda before vowel
        $vowels = implode('', Config::SHORT_VOWELS);
        $pattern = "/([{$vowels}])(" . Config::SHADDA . ")/u";
        $text = preg_replace($pattern, '$2$1', $text) ?? $text;

        // Tanween before shadda -> shadda before tanween
        $tanween = implode('', Config::TANWEEN);
        $pattern = "/([{$tanween}])(" . Config::SHADDA . ")/u";
        $text = preg_replace($pattern, '$2$1', $text) ?? $text;

        return $text;
    }

    /**
     * Remove only short vowels (fatha, damma, kasra)
     */
    public function removeShortVowels(string $text): string
    {
        $pattern = '[' . implode('', Config::SHORT_VOWELS) . ']';
        return preg_replace("/{$pattern}/u", '', $text) ?? $text;
    }

    /**
     * Remove only tanween
     */
    public function removeTanween(string $text): string
    {
        $pattern = '[' . implode('', Config::TANWEEN) . ']';
        return preg_replace("/{$pattern}/u", '', $text) ?? $text;
    }

    /**
     * Remove shadda only
     */
    public function removeShadda(string $text): string
    {
        return str_replace(Config::SHADDA, '', $text);
    }

    /**
     * Get diacritic by name
     */
    public function getDiacriticByName(string $name): ?string
    {
        $nameMap = array_flip(Config::DIACRITIC_NAMES);
        return $nameMap[$name] ?? null;
    }

    /**
     * Get diacritic Arabic name
     */
    public function getDiacriticArabicName(string $diacritic): string
    {
        return Config::DIACRITIC_NAMES_AR[$diacritic] ?? 'غير معروف';
    }

    /**
     * Check if letter is a sun letter
     */
    public function isSunLetter(string $letter): bool
    {
        return in_array($letter, Config::SUN_LETTERS, true);
    }

    /**
     * Check if letter is a moon letter
     */
    public function isMoonLetter(string $letter): bool
    {
        return in_array($letter, Config::MOON_LETTERS, true);
    }

    /**
     * Get tashkeel density (ratio of diacritics to letters)
     */
    public function getTashkeelDensity(string $text): float
    {
        $letterCount = 0;
        $diacriticCount = 0;
        $length = mb_strlen($text);

        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($text, $i, 1);
            if (in_array($char, Config::ARABIC_LETTERS, true)) {
                $letterCount++;
            } elseif (in_array($char, Config::ALL_DIACRITICS, true)) {
                $diacriticCount++;
            }
        }

        if ($letterCount === 0) {
            return 0.0;
        }

        return round($diacriticCount / $letterCount, 4);
    }

    /**
     * Convert mb string to array
     *
     * @return array<string>
     */
    private function mbStringToArray(string $string): array
    {
        $result = [];
        $length = mb_strlen($string);

        for ($i = 0; $i < $length; $i++) {
            $result[] = mb_substr($string, $i, 1);
        }

        return $result;
    }
}
