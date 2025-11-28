<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Arabic;
use ArPHP\Core\Contracts\ServiceInterface;

/**
 * Transliteration Service - Arabic-Latin Conversion
 * 
 * Converts between Arabic script and Latin characters (Romanization)
 * Supports multiple standards: ALA-LC, Buckwalter, ISO 233
 */
class TransliterationService implements ServiceInterface
{
    /**
     * Arabic to Latin mapping (ALA-LC standard - default)
     * 
     * @var array<string, string>
     */
    private const ARABIC_TO_LATIN_ALA = [
        'ا' => 'a',
        'أ' => 'a',
        'إ' => 'i',
        'آ' => 'aa',
        'ب' => 'b',
        'ت' => 't',
        'ث' => 'th',
        'ج' => 'j',
        'ح' => 'h',
        'خ' => 'kh',
        'د' => 'd',
        'ذ' => 'dh',
        'ر' => 'r',
        'ز' => 'z',
        'س' => 's',
        'ش' => 'sh',
        'ص' => 's',
        'ض' => 'd',
        'ط' => 't',
        'ظ' => 'z',
        'ع' => 'a',
        'غ' => 'gh',
        'ف' => 'f',
        'ق' => 'q',
        'ك' => 'k',
        'ل' => 'l',
        'م' => 'm',
        'ن' => 'n',
        'ه' => 'h',
        'ة' => 'h',
        'و' => 'w',
        'ي' => 'y',
        'ى' => 'a',
        'ئ' => 'e',
        'ء' => 'a',
        'ؤ' => 'o',
        ' ' => ' ',
    ];

    /**
     * Arabic to Latin mapping (Buckwalter standard)
     * 
     * @var array<string, string>
     */
    private const ARABIC_TO_LATIN_BUCKWALTER = [
        'ا' => 'A', 'أ' => '>', 'إ' => '<', 'آ' => '|', 'ب' => 'b',
        'ت' => 't', 'ث' => 'v', 'ج' => 'j', 'ح' => 'H', 'خ' => 'x',
        'د' => 'd', 'ذ' => '*', 'ر' => 'r', 'ز' => 'z', 'س' => 's',
        'ش' => '$', 'ص' => 'S', 'ض' => 'D', 'ط' => 'T', 'ظ' => 'Z',
        'ع' => 'E', 'غ' => 'g', 'ف' => 'f', 'ق' => 'q', 'ك' => 'k',
        'ل' => 'l', 'م' => 'm', 'ن' => 'n', 'ه' => 'h', 'ة' => 'p',
        'و' => 'w', 'ي' => 'y', 'ى' => 'Y', 'ئ' => '}', 'ء' => '\'',
        'ؤ' => '&', ' ' => ' ',
    ];

    /**
     * Arabic to Latin mapping (ISO 233 standard)
     * 
     * @var array<string, string>
     */
    private const ARABIC_TO_LATIN_ISO = [
        'ا' => 'ā', 'أ' => 'a', 'إ' => 'i', 'آ' => 'ā', 'ب' => 'b',
        'ت' => 't', 'ث' => 'ṯ', 'ج' => 'ǧ', 'ح' => 'ḥ', 'خ' => 'ḫ',
        'د' => 'd', 'ذ' => 'ḏ', 'ر' => 'r', 'ز' => 'z', 'س' => 's',
        'ش' => 'š', 'ص' => 'ṣ', 'ض' => 'ḍ', 'ط' => 'ṭ', 'ظ' => 'ẓ',
        'ع' => 'ʿ', 'غ' => 'ġ', 'ف' => 'f', 'ق' => 'q', 'ك' => 'k',
        'ل' => 'l', 'م' => 'm', 'ن' => 'n', 'ه' => 'h', 'ة' => 'ẗ',
        'و' => 'w', 'ي' => 'y', 'ى' => 'ā', 'ئ' => 'y', 'ء' => 'ʾ',
        'ؤ' => 'w', ' ' => ' ',
    ];

    /**
     * Latin to Arabic mapping (simplified)
     * 
     * @var array<string, string>
     */
    private const LATIN_TO_ARABIC = [
        'aa' => 'آ',
        'th' => 'ث',
        'kh' => 'خ',
        'dh' => 'ذ',
        'sh' => 'ش',
        'gh' => 'غ',
        'a' => 'ا',
        'b' => 'ب',
        't' => 'ت',
        'j' => 'ج',
        'h' => 'ح',
        'd' => 'د',
        'r' => 'ر',
        'z' => 'ز',
        's' => 'س',
        'f' => 'ف',
        'q' => 'ق',
        'k' => 'ك',
        'l' => 'ل',
        'm' => 'م',
        'n' => 'ن',
        'w' => 'و',
        'y' => 'ي',
        'e' => 'ع',
        'o' => 'و',
        'i' => 'ي',
        ' ' => ' ',
    ];

    /**
     * Convert Arabic text to Latin characters
     * 
     * @param string $standard 'ala' (default), 'buckwalter', or 'iso'
     */
    public function toLatin(string $text, string $standard = 'ala'): string
    {
        $mapping = match (strtolower($standard)) {
            'buckwalter', 'bw' => self::ARABIC_TO_LATIN_BUCKWALTER,
            'iso', 'iso233' => self::ARABIC_TO_LATIN_ISO,
            default => self::ARABIC_TO_LATIN_ALA,
        };

        $result = '';
        $length = mb_strlen($text, 'UTF-8');

        for ($i = 0; $i < $length; $i++) {
            $char = mb_substr($text, $i, 1, 'UTF-8');
            $result .= $mapping[$char] ?? $char;
        }

        return $result;
    }

    /**
     * Convert multiple texts to Latin (batch processing)
     * 
     * @param array<int, string> $texts
     * @return array<int, string>
     */
    public function toLatinBatch(array $texts, string $standard = 'ala'): array
    {
        return array_map(fn($text) => $this->toLatin($text, $standard), $texts);
    }

    /**
     * Convert Latin text to Arabic characters
     */
    public function toArabic(string $text): string
    {
        $text = mb_strtolower($text, 'UTF-8');
        
        // Sort by length (longest first) to match 'th', 'kh' before 't', 'k'
        $patterns = self::LATIN_TO_ARABIC;
        uksort($patterns, fn($a, $b) => mb_strlen($b) - mb_strlen($a));

        foreach ($patterns as $latin => $arabic) {
            $text = str_replace($latin, $arabic, $text);
        }

        return $text;
    }

    /**
     * Convert multiple Latin texts to Arabic (batch processing)
     * 
     * @param array<int, string> $texts
     * @return array<int, string>
     */
    public function toArabicBatch(array $texts): array
    {
        return array_map(fn($text) => $this->toArabic($text), $texts);
    }

    /**
     * Detect if text is Arabic
     */
    public function isArabic(string $text): bool
    {
        return preg_match('/[\x{0600}-\x{06FF}]/u', $text) === 1;
    }

    /**
     * Detect if text is Latin
     */
    public function isLatin(string $text): bool
    {
        return preg_match('/^[a-zA-Z\s]+$/', $text) === 1;
    }

    /**
     * Auto-detect and convert
     */
    public function convert(string $text): string
    {
        if ($this->isArabic($text)) {
            return $this->toLatin($text);
        } elseif ($this->isLatin($text)) {
            return $this->toArabic($text);
        }
        
        return $text;
    }

    public function getServiceName(): string
    {
        return 'transliteration';
    }

    public function getConfig(): array
    {
        return [
            'version' => '2.0.0',
            'standards' => ['ALA-LC', 'Buckwalter', 'ISO-233'],
            'features' => [
                'toLatin',
                'toLatinBatch',
                'toArabic',
                'toArabicBatch',
                'isArabic',
                'isLatin',
                'convert',
            ],
        ];
    }

    public function isAvailable(): bool
    {
        return extension_loaded('mbstring');
    }
}

/**
 * Transliteration Module
 * 
 * Registers the Transliteration service for Arabic-Latin conversion
 */
class TransliterationModule extends AbstractModule
{
    protected string $version = '1.0.0';

    public function getName(): string
    {
        return 'transliteration';
    }

    public function register(): void
    {
        Arabic::container()->register('transliteration', function () {
            return new TransliterationService();
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
