<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\BuckwalterTransliteration\Services;

use ArPHP\Core\Modules\BuckwalterTransliteration\Config;
use ArPHP\Core\Modules\BuckwalterTransliteration\Contracts\BuckwalterTransliterationInterface;
use ArPHP\Core\Modules\BuckwalterTransliteration\Exceptions\BuckwalterTransliterationException;

/**
 * Buckwalter Transliteration Service - PHP 8.4
 *
 * @package ArPHP\Core\Modules\BuckwalterTransliteration
 */
final class BuckwalterTransliterationService implements BuckwalterTransliterationInterface
{
    /**
     * @inheritDoc
     */
    public function toLatinBuckwalter(string $text): string
    {
        return $this->transliterate($text, Config::BUCKWALTER_ARABIC_TO_LATIN);
    }

    /**
     * @inheritDoc
     */
    public function toArabicBuckwalter(string $text): string
    {
        return $this->transliterate($text, Config::BUCKWALTER_LATIN_TO_ARABIC);
    }

    /**
     * @inheritDoc
     */
    public function toLatinSafeBuckwalter(string $text): string
    {
        return $this->transliterate($text, Config::SAFE_BUCKWALTER_ARABIC_TO_LATIN);
    }

    /**
     * @inheritDoc
     */
    public function fromSafeBuckwalter(string $text): string
    {
        $reverseMap = \array_flip(Config::SAFE_BUCKWALTER_ARABIC_TO_LATIN);

        return $this->transliterate($text, $reverseMap);
    }

    /**
     * @inheritDoc
     */
    public function toLatinIso233(string $text): string
    {
        return $this->transliterate($text, Config::ISO233_ARABIC_TO_LATIN);
    }

    /**
     * @inheritDoc
     */
    public function toLatinDin31635(string $text): string
    {
        // DIN 31635 is similar to ISO 233 with minor differences
        $din31635Map = Config::ISO233_ARABIC_TO_LATIN;
        $din31635Map['ج'] = 'ğ';
        $din31635Map['ة'] = 'a';

        return $this->transliterate($text, $din31635Map);
    }

    /**
     * @inheritDoc
     */
    public function toLatinLoc(string $text): string
    {
        // Library of Congress romanization
        $locMap = [
            'ء' => 'ʼ',
            'آ' => 'ā',
            'أ' => 'a',
            'ؤ' => 'ʼ',
            'إ' => 'i',
            'ئ' => 'ʼ',
            'ا' => 'ā',
            'ب' => 'b',
            'ة' => 'ah',
            'ت' => 't',
            'ث' => 'th',
            'ج' => 'j',
            'ح' => 'ḥ',
            'خ' => 'kh',
            'د' => 'd',
            'ذ' => 'dh',
            'ر' => 'r',
            'ز' => 'z',
            'س' => 's',
            'ش' => 'sh',
            'ص' => 'ṣ',
            'ض' => 'ḍ',
            'ط' => 'ṭ',
            'ظ' => 'ẓ',
            'ع' => 'ʻ',
            'غ' => 'gh',
            'ف' => 'f',
            'ق' => 'q',
            'ك' => 'k',
            'ل' => 'l',
            'م' => 'm',
            'ن' => 'n',
            'ه' => 'h',
            'و' => 'w',
            'ى' => 'á',
            'ي' => 'y',
        ];

        return $this->transliterate($text, $locMap);
    }

    /**
     * @inheritDoc
     */
    public function toPhonetic(string $text): string
    {
        return $this->transliterate($text, Config::PHONETIC_ARABIC_TO_LATIN);
    }

    /**
     * @inheritDoc
     */
    public function toArabic(string $text, string $scheme): string
    {
        $map = $this->getSchemeReverseMap($scheme);

        return $this->transliterate($text, $map);
    }

    /**
     * @inheritDoc
     */
    public function getSchemes(): array
    {
        return Config::SCHEMES;
    }

    /**
     * Perform transliteration using a character map
     *
     * @param array<string, string> $map
     */
    private function transliterate(string $text, array $map): string
    {
        if ($text === '') {
            return '';
        }

        // Sort by key length descending for multi-char mappings
        \uksort($map, fn($a, $b) => \mb_strlen($b) <=> \mb_strlen($a));

        $result = '';
        $length = \mb_strlen($text, 'UTF-8');
        $i = 0;

        while ($i < $length) {
            $matched = false;

            // Try to match longer sequences first
            foreach ($map as $from => $to) {
                $fromLen = \mb_strlen($from, 'UTF-8');
                $substr = \mb_substr($text, $i, $fromLen, 'UTF-8');

                if ($substr === $from) {
                    $result .= $to;
                    $i += $fromLen;
                    $matched = true;
                    break;
                }
            }

            if (!$matched) {
                $result .= \mb_substr($text, $i, 1, 'UTF-8');
                $i++;
            }
        }

        return $result;
    }

    /**
     * Get reverse mapping for a scheme
     *
     * @return array<string, string>
     */
    private function getSchemeReverseMap(string $scheme): array
    {
        return match ($scheme) {
            Config::SCHEME_BUCKWALTER => Config::BUCKWALTER_LATIN_TO_ARABIC,
            Config::SCHEME_SAFE_BUCKWALTER => \array_flip(Config::SAFE_BUCKWALTER_ARABIC_TO_LATIN),
            Config::SCHEME_ISO233 => \array_flip(Config::ISO233_ARABIC_TO_LATIN),
            Config::SCHEME_PHONETIC => $this->buildReversePhoneticMap(),
            default => throw BuckwalterTransliterationException::unsupportedScheme($scheme),
        };
    }

    /**
     * Build reverse phonetic mapping
     *
     * @return array<string, string>
     */
    private function buildReversePhoneticMap(): array
    {
        // Phonetic reverse is not 1:1, use best approximations
        return [
            'th' => 'ث',
            'kh' => 'خ',
            'dh' => 'ذ',
            'sh' => 'ش',
            'gh' => 'غ',
            'aa' => 'آ',
            'an' => 'ً',
            'un' => 'ٌ',
            'in' => 'ٍ',
            "'" => 'ء',
            'a' => 'ا',
            'b' => 'ب',
            't' => 'ت',
            'j' => 'ج',
            'h' => 'ه',
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
            'e' => 'إ',
            'o' => 'ؤ',
            'u' => 'ُ',
            'i' => 'ِ',
        ];
    }

    /**
     * Detect transliteration scheme from text
     */
    public function detectScheme(string $text): ?string
    {
        // Check for Buckwalter-specific characters
        if (\preg_match('/[|><}&$*~]/', $text)) {
            return Config::SCHEME_BUCKWALTER;
        }

        // Check for ISO 233 diacritics
        if (\preg_match('/[āṯǧḥḫḏšṣḍṭẓġʾʿ]/u', $text)) {
            return Config::SCHEME_ISO233;
        }

        // Check for LOC specific
        if (\preg_match('/[ḥṣḍṭẓʻ]/u', $text)) {
            return Config::SCHEME_LOC;
        }

        return null;
    }

    /**
     * Check if text contains Arabic characters
     */
    public function containsArabic(string $text): bool
    {
        return (bool) \preg_match('/[\x{0600}-\x{06FF}]/u', $text);
    }

    /**
     * Check if text is valid Buckwalter
     */
    public function isValidBuckwalter(string $text): bool
    {
        $validChars = \array_keys(Config::BUCKWALTER_LATIN_TO_ARABIC);
        $validChars = \array_merge($validChars, [' ', "\n", "\r", "\t", '0', '1', '2', '3', '4', '5', '6', '7', '8', '9']);

        $length = \strlen($text);

        for ($i = 0; $i < $length; $i++) {
            if (!\in_array($text[$i], $validChars, true)) {
                return false;
            }
        }

        return true;
    }
}
