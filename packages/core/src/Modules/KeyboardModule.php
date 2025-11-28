<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Arabic;
use ArPHP\Core\Contracts\ServiceInterface;

/**
 * Keyboard Correction Service
 * 
 * Fixes common keyboard layout mistakes
 */
class KeyboardService implements ServiceInterface
{
    /**
     * English to Arabic keyboard mapping
     * 
     * @var array<string, string>
     */
    private const EN_TO_AR = [
        'q' => 'ض', 'w' => 'ص', 'e' => 'ث', 'r' => 'ق', 't' => 'ف',
        'y' => 'غ', 'u' => 'ع', 'i' => 'ه', 'o' => 'خ', 'p' => 'ح',
        '[' => 'ج', ']' => 'د',
        'a' => 'ش', 's' => 'س', 'd' => 'ي', 'f' => 'ب', 'g' => 'ل',
        'h' => 'ا', 'j' => 'ت', 'k' => 'ن', 'l' => 'م', ';' => 'ك',
        '\'' => 'ط',
        'z' => 'ئ', 'x' => 'ء', 'c' => 'ؤ', 'v' => 'ر', 'b' => 'لا',
        'n' => 'ى', 'm' => 'ة', ',' => 'و', '.' => 'ز', '/' => 'ظ',
        '`' => 'ذ',
        // Shifted characters
        'Q' => 'َ', 'W' => 'ً', 'E' => 'ُ', 'R' => 'ٌ', 'T' => 'لإ',
        'Y' => 'إ', 'U' => '\'', 'I' => '÷', 'O' => '×', 'P' => '؛',
        'A' => 'ِ', 'S' => 'ٍ', 'D' => ']', 'F' => '[', 'G' => 'لأ',
        'H' => 'أ', 'J' => 'ـ', 'K' => '،', 'L' => '/', 'Z' => '~',
        'X' => 'ْ', 'C' => '}', 'V' => '{', 'B' => 'لآ',
        'N' => 'آ', 'M' => '\'', '>' => ',', '<' => '.', '?' => '؟',
    ];

    /**
     * Arabic to English keyboard mapping
     * 
     * @var array<string, string>
     */
    private const AR_TO_EN = [
        'ض' => 'q', 'ص' => 'w', 'ث' => 'e', 'ق' => 'r', 'ف' => 't',
        'غ' => 'y', 'ع' => 'u', 'ه' => 'i', 'خ' => 'o', 'ح' => 'p',
        'ج' => '[', 'د' => ']',
        'ش' => 'a', 'س' => 's', 'ي' => 'd', 'ب' => 'f', 'ل' => 'g',
        'ا' => 'h', 'ت' => 'j', 'ن' => 'k', 'م' => 'l', 'ك' => ';',
        'ط' => '\'',
        'ئ' => 'z', 'ء' => 'x', 'ؤ' => 'c', 'ر' => 'v', 'لا' => 'b',
        'ى' => 'n', 'ة' => 'm', 'و' => ',', 'ز' => '.', 'ظ' => '/',
        'ذ' => '`',
    ];

    /**
     * Fix English typed as Arabic
     * 
     * Example: "lhv hggi" -> "بسم الله"
     */
    public function fixEnglishTypedAsArabic(string $text): string
    {
        $result = '';
        $length = mb_strlen($text, 'UTF-8');
        
        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($text, $i, 1, 'UTF-8');
            $result .= self::EN_TO_AR[$char] ?? $char;
        }
        
        return $result;
    }

    /**
     * Fix Arabic typed as English
     * 
     * Example: "مخممثق" -> "hello"
     */
    public function fixArabicTypedAsEnglish(string $text): string
    {
        $result = '';
        $length = mb_strlen($text, 'UTF-8');
        
        // Check for 'لا' first (2 chars mapped to 1)
        $i = 0;
        while ($i < $length) {
            $char = mb_substr($text, $i, 1, 'UTF-8');
            $nextChar = $i + 1 < $length ? mb_substr($text, $i + 1, 1, 'UTF-8') : '';
            
            // Check for 'لا'
            if ($char === 'ل' && $nextChar === 'ا') {
                $result .= 'b';
                $i += 2;
                continue;
            }
            
            $result .= self::AR_TO_EN[$char] ?? $char;
            $i++;
        }
        
        return $result;
    }

    /**
     * Auto-detect and fix keyboard layout
     */
    public function autoFix(string $text): string
    {
        // Detect if text is mostly English or Arabic
        $arabicChars = preg_match_all('/[\p{Arabic}]/u', $text);
        $englishChars = preg_match_all('/[a-zA-Z]/', $text);
        
        $totalChars = $arabicChars + $englishChars;
        
        if ($totalChars === 0) {
            return $text;
        }
        
        // If mostly English but should be Arabic
        if ($englishChars > $arabicChars && $englishChars / $totalChars > 0.7) {
            return $this->fixEnglishTypedAsArabic($text);
        }
        
        // If mostly Arabic but should be English  
        if ($arabicChars > $englishChars && $arabicChars / $totalChars > 0.7) {
            return $this->fixArabicTypedAsEnglish($text);
        }
        
        return $text;
    }

    /**
     * Check if text looks like wrong keyboard layout
     */
    public function isWrongLayout(string $text): bool
    {
        // Check for common patterns that indicate wrong layout
        $patterns = [
            '/^[a-z\[\];\',\.\/`]+$/i',  // All English keyboard chars
            '/^[\p{Arabic}]+$/u',         // All Arabic chars
        ];
        
        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text) === 1) {
                // Check if it makes sense
                $fixed = $this->autoFix($text);
                return $fixed !== $text;
            }
        }
        
        return false;
    }

    /**
     * Get both versions (original and fixed)
     * 
     * @return array{original: string, fixed: string, was_fixed: bool}
     */
    public function getSuggestion(string $text): array
    {
        $fixed = $this->autoFix($text);
        
        return [
            'original' => $text,
            'fixed' => $fixed,
            'was_fixed' => $fixed !== $text,
        ];
    }

    public function getServiceName(): string
    {
        return 'keyboard';
    }

    public function getConfig(): array
    {
        return [
            'version' => '1.0.0',
            'features' => [
                'fixEnglishTypedAsArabic',
                'fixArabicTypedAsEnglish',
                'autoFix',
                'isWrongLayout',
                'getSuggestion',
            ],
        ];
    }

    public function isAvailable(): bool
    {
        return extension_loaded('mbstring');
    }
}

/**
 * Keyboard Module
 * 
 * Registers the Keyboard service for layout correction
 */
class KeyboardModule extends AbstractModule
{
    protected string $version = '1.0.0';

    public function getName(): string
    {
        return 'keyboard';
    }

    public function register(): void
    {
        Arabic::container()->register('keyboard', function () {
            return new KeyboardService();
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
