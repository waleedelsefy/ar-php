<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\AdvancedKeyboardFix\Services;

use ArPHP\Core\Modules\AdvancedKeyboardFix\Config;
use ArPHP\Core\Modules\AdvancedKeyboardFix\Contracts\AdvancedKeyboardFixInterface;
use ArPHP\Core\Modules\AdvancedKeyboardFix\Exceptions\AdvancedKeyboardFixException;

/**
 * Advanced Keyboard Fix Service - PHP 8.4
 *
 * @package ArPHP\Core\Modules\AdvancedKeyboardFix
 */
final class AdvancedKeyboardFixService implements AdvancedKeyboardFixInterface
{
    /** @var array<string, string> */
    private array $englishToArabic;

    /** @var array<string, string> */
    private array $arabicToEnglish;

    public function __construct()
    {
        $this->englishToArabic = Config::ENGLISH_TO_ARABIC;
        $this->arabicToEnglish = Config::ARABIC_TO_ENGLISH;
    }

    /**
     * @inheritDoc
     */
    public function fixArabicOnEnglish(string $text): string
    {
        if ($text === '') {
            return '';
        }

        $result = '';
        $length = \mb_strlen($text, 'UTF-8');

        for ($i = 0; $i < $length; $i++) {
            $char = \mb_substr($text, $i, 1, 'UTF-8');

            if (isset($this->englishToArabic[$char])) {
                $result .= $this->englishToArabic[$char];
            } else {
                $result .= $char;
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function fixEnglishOnArabic(string $text): string
    {
        if ($text === '') {
            return '';
        }

        $result = '';
        $length = \mb_strlen($text, 'UTF-8');

        for ($i = 0; $i < $length; $i++) {
            $char = \mb_substr($text, $i, 1, 'UTF-8');

            if (isset($this->arabicToEnglish[$char])) {
                $result .= $this->arabicToEnglish[$char];
            } else {
                $result .= $char;
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function autoFix(string $text): string
    {
        if ($text === '') {
            return '';
        }

        $layout = $this->detectLayout($text);

        return match ($layout) {
            Config::LAYOUT_ENGLISH => $this->fixArabicOnEnglish($text),
            Config::LAYOUT_ARABIC => $text, // Already Arabic
            Config::LAYOUT_MIXED => $this->fixMixedText($text),
            default => $text,
        };
    }

    /**
     * @inheritDoc
     */
    public function detectLayout(string $text): string
    {
        if ($text === '') {
            return Config::LAYOUT_UNKNOWN;
        }

        $totalChars = 0;
        $arabicChars = 0;
        $englishChars = 0;

        $length = \mb_strlen($text, 'UTF-8');

        for ($i = 0; $i < $length; $i++) {
            $char = \mb_substr($text, $i, 1, 'UTF-8');

            // Skip whitespace and numbers
            if (\preg_match('/[\s\d]/u', $char)) {
                continue;
            }

            $totalChars++;

            // Check if Arabic
            if (\preg_match('/[\x{0600}-\x{06FF}]/u', $char)) {
                $arabicChars++;
            }
            // Check if English letter
            elseif (\preg_match('/[a-zA-Z]/u', $char)) {
                $englishChars++;
            }
        }

        if ($totalChars === 0) {
            return Config::LAYOUT_UNKNOWN;
        }

        $arabicRatio = $arabicChars / $totalChars;
        $englishRatio = $englishChars / $totalChars;

        if ($arabicRatio >= Config::ARABIC_THRESHOLD) {
            return Config::LAYOUT_ARABIC;
        }

        if ($englishRatio >= Config::ENGLISH_THRESHOLD) {
            return Config::LAYOUT_ENGLISH;
        }

        if ($arabicChars > 0 && $englishChars > 0) {
            return Config::LAYOUT_MIXED;
        }

        return Config::LAYOUT_UNKNOWN;
    }

    /**
     * @inheritDoc
     */
    public function hasLayoutIssue(string $text): bool
    {
        // Check for common patterns that indicate wrong keyboard
        // English letters that look like they should be Arabic
        $suspiciousPatterns = [
            // Common Arabic words typed on English keyboard
            '/hgph/',     // الله
            '/hgsjg/',    // السلا
            '/wfhp/',     // صباح
            '/lsvh/',     // مسرا
        ];

        foreach ($suspiciousPatterns as $pattern) {
            if (\preg_match($pattern . '/i', $text)) {
                return true;
            }
        }

        // Check for Arabic letters mixed with QWERTY keyboard positions
        return $this->detectLayout($text) === Config::LAYOUT_MIXED;
    }

    /**
     * @inheritDoc
     */
    public function fixFrancoArabic(string $text): string
    {
        if ($text === '') {
            return '';
        }

        $text = \mb_strtolower($text, 'UTF-8');

        // Sort by length descending to match longer patterns first
        $francoMap = Config::FRANCO_TO_ARABIC;
        \uksort($francoMap, fn($a, $b) => \mb_strlen($b) <=> \mb_strlen($a));

        foreach ($francoMap as $franco => $arabic) {
            $text = \str_replace($franco, $arabic, $text);
        }

        // Handle numbers in context
        $text = \preg_replace_callback(
            '/(\d)(?=[ابتثجحخدذرزسشصضطظعغفقكلمنهوي])/u',
            function ($matches) {
                return match ($matches[1]) {
                    '2' => 'ء',
                    '3' => 'ع',
                    '5' => 'خ',
                    '6' => 'ط',
                    '7' => 'ح',
                    '8' => 'ق',
                    '9' => 'ص',
                    default => $matches[1],
                };
            },
            $text
        );

        return $text;
    }

    /**
     * @inheritDoc
     */
    public function fixTypingMistakes(string $text): string
    {
        if ($text === '') {
            return '';
        }

        // Fix multiple spaces
        $text = \preg_replace('/\s+/u', ' ', $text);

        // Fix common mistakes
        foreach (Config::TYPING_MISTAKES as $mistake => $correction) {
            $text = \str_replace($mistake, $correction, $text);
        }

        // Fix misplaced diacritics
        $text = $this->fixDiacriticPlacement($text);

        return \trim($text);
    }

    /**
     * @inheritDoc
     */
    public function getKeyboardMap(string $layout): array
    {
        return match ($layout) {
            Config::LAYOUT_ARABIC => $this->arabicToEnglish,
            Config::LAYOUT_ENGLISH => $this->englishToArabic,
            default => throw AdvancedKeyboardFixException::unsupportedLayout($layout),
        };
    }

    /**
     * Fix text with mixed Arabic/English layout issues
     */
    private function fixMixedText(string $text): string
    {
        // Split into words and fix each based on context
        $words = \preg_split('/(\s+)/u', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        $result = '';

        foreach ($words as $word) {
            if (\preg_match('/^\s+$/u', $word)) {
                $result .= $word;
                continue;
            }

            $layout = $this->detectLayout($word);

            if ($layout === Config::LAYOUT_ENGLISH && $this->looksLikeArabicOnEnglish($word)) {
                $result .= $this->fixArabicOnEnglish($word);
            } else {
                $result .= $word;
            }
        }

        return $result;
    }

    /**
     * Check if English text looks like Arabic typed on English keyboard
     */
    private function looksLikeArabicOnEnglish(string $text): bool
    {
        // Common patterns
        $patterns = [
            '/^hg[a-z]+$/i',  // "ال" prefix
            '/[qweryuiop\[\]asdfghjkl;\'zxcvbnm,.\/]+$/i', // Main keyboard area
        ];

        foreach ($patterns as $pattern) {
            if (\preg_match($pattern, $text)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Fix misplaced diacritics
     */
    private function fixDiacriticPlacement(string $text): string
    {
        // Diacritics should follow letters, not precede them
        $diacritics = [
            "\u{064B}", "\u{064C}", "\u{064D}", "\u{064E}",
            "\u{064F}", "\u{0650}", "\u{0651}", "\u{0652}",
        ];

        foreach ($diacritics as $d) {
            // Fix diacritic at start of word
            $text = \preg_replace('/(?<=\s)' . \preg_quote($d, '/') . '/u', '', $text);
            // Fix double diacritics (except shadda combinations)
            $text = \preg_replace('/' . \preg_quote($d, '/') . '{2,}/u', $d, $text);
        }

        return $text;
    }

    /**
     * Convert text between layouts
     */
    public function convertLayout(string $text, string $from, string $to): string
    {
        if ($from === $to) {
            return $text;
        }

        if ($from === Config::LAYOUT_ENGLISH && $to === Config::LAYOUT_ARABIC) {
            return $this->fixArabicOnEnglish($text);
        }

        if ($from === Config::LAYOUT_ARABIC && $to === Config::LAYOUT_ENGLISH) {
            return $this->fixEnglishOnArabic($text);
        }

        return $text;
    }

    /**
     * Get suggestion for text correction
     *
     * @return array{original: string, suggested: string, confidence: float}
     */
    public function getSuggestion(string $text): array
    {
        $layout = $this->detectLayout($text);
        $confidence = 0.0;
        $suggested = $text;

        if ($layout === Config::LAYOUT_ENGLISH && $this->hasLayoutIssue($text)) {
            $suggested = $this->fixArabicOnEnglish($text);
            $confidence = 0.8;
        } elseif ($layout === Config::LAYOUT_MIXED) {
            $suggested = $this->fixMixedText($text);
            $confidence = 0.6;
        }

        return [
            'original' => $text,
            'suggested' => $suggested,
            'confidence' => $confidence,
        ];
    }
}
