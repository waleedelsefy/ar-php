<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Normalization\Services;

use ArPHP\Core\Modules\Normalization\Config;
use ArPHP\Core\Modules\Normalization\Contracts\NormalizationInterface;
use ArPHP\Core\Modules\Normalization\Exceptions\NormalizationException;

/**
 * Normalization Service - PHP 8.4
 *
 * @package ArPHP\Core\Modules\Normalization
 */
final class NormalizationService implements NormalizationInterface
{
    /**
     * @inheritDoc
     */
    public function normalize(string $text): string
    {
        $text = $this->removeDiacritics($text);
        $text = $this->normalizeAlef($text);
        $text = $this->normalizeTaMarbuta($text);
        $text = $this->normalizeAlefMaqsura($text);
        $text = $this->removeTatweel($text);
        $text = $this->normalizeWhitespace($text);

        return $text;
    }

    /**
     * @inheritDoc
     */
    public function removeDiacritics(string $text): string
    {
        return \str_replace(Config::DIACRITICS, '', $text);
    }

    /**
     * @inheritDoc
     */
    public function normalizeAlef(string $text): string
    {
        return \str_replace(Config::ALEF_VARIANTS, Config::ALEF_NORMAL, $text);
    }

    /**
     * @inheritDoc
     */
    public function normalizeTaMarbuta(string $text): string
    {
        return \str_replace(Config::TA_MARBUTA, Config::HA, $text);
    }

    /**
     * @inheritDoc
     */
    public function normalizeAlefMaqsura(string $text): string
    {
        return \str_replace(Config::ALEF_MAQSURA, Config::YAA, $text);
    }

    /**
     * @inheritDoc
     */
    public function normalizeWaw(string $text): string
    {
        return \str_replace(Config::WAW_VARIANTS, Config::WAW_NORMAL, $text);
    }

    /**
     * @inheritDoc
     */
    public function normalizeYaa(string $text): string
    {
        return \str_replace(Config::YAA_VARIANTS, Config::YAA, $text);
    }

    /**
     * @inheritDoc
     */
    public function removeTatweel(string $text): string
    {
        return \str_replace(Config::TATWEEL, '', $text);
    }

    /**
     * @inheritDoc
     */
    public function removeNonArabic(string $text): string
    {
        return \preg_replace('/[^' . Config::ARABIC_RANGE . '\s]/u', '', $text) ?? $text;
    }

    /**
     * @inheritDoc
     */
    public function normalizeWhitespace(string $text): string
    {
        // Replace multiple whitespace with single space
        $text = \preg_replace('/\s+/u', ' ', $text);

        return \trim($text);
    }

    /**
     * @inheritDoc
     */
    public function normalizeNumbers(string $text, string $style = 'arabic'): string
    {
        if (!\in_array($style, [Config::STYLE_ARABIC, Config::STYLE_WESTERN], true)) {
            throw NormalizationException::invalidNumberStyle($style);
        }

        if ($style === Config::STYLE_ARABIC) {
            // Western to Arabic-Indic
            $text = \str_replace(Config::WESTERN_DIGITS, Config::ARABIC_INDIC_DIGITS, $text);
            // Extended Arabic to Arabic-Indic
            $text = \str_replace(Config::EXTENDED_ARABIC_DIGITS, Config::ARABIC_INDIC_DIGITS, $text);
        } else {
            // Arabic-Indic to Western
            $text = \str_replace(Config::ARABIC_INDIC_DIGITS, Config::WESTERN_DIGITS, $text);
            // Extended Arabic to Western
            $text = \str_replace(Config::EXTENDED_ARABIC_DIGITS, Config::WESTERN_DIGITS, $text);
        }

        return $text;
    }

    /**
     * @inheritDoc
     */
    public function normalizeForSearch(string $text): string
    {
        $text = $this->normalize($text);
        $text = \mb_strtolower($text, 'UTF-8');

        return $text;
    }

    /**
     * @inheritDoc
     */
    public function normalizeCustom(string $text, array $options): string
    {
        if ($options[Config::OPTION_DIACRITICS] ?? false) {
            $text = $this->removeDiacritics($text);
        }

        if ($options[Config::OPTION_ALEF] ?? false) {
            $text = $this->normalizeAlef($text);
        }

        if ($options[Config::OPTION_TA_MARBUTA] ?? false) {
            $text = $this->normalizeTaMarbuta($text);
        }

        if ($options[Config::OPTION_ALEF_MAQSURA] ?? false) {
            $text = $this->normalizeAlefMaqsura($text);
        }

        if ($options[Config::OPTION_WAW] ?? false) {
            $text = $this->normalizeWaw($text);
        }

        if ($options[Config::OPTION_YAA] ?? false) {
            $text = $this->normalizeYaa($text);
        }

        if ($options[Config::OPTION_TATWEEL] ?? false) {
            $text = $this->removeTatweel($text);
        }

        if ($options[Config::OPTION_WHITESPACE] ?? false) {
            $text = $this->normalizeWhitespace($text);
        }

        if (isset($options[Config::OPTION_NUMBERS])) {
            $text = $this->normalizeNumbers($text, $options[Config::OPTION_NUMBERS]);
        }

        return $text;
    }

    /**
     * Remove Hamza
     */
    public function removeHamza(string $text): string
    {
        $hamzaVariants = ['ء', 'أ', 'إ', 'ؤ', 'ئ', 'آ'];
        $replacements = ['', 'ا', 'ا', 'و', 'ي', 'ا'];

        return \str_replace($hamzaVariants, $replacements, $text);
    }

    /**
     * Normalize Hamza
     */
    public function normalizeHamza(string $text): string
    {
        // أ، إ → ا
        // ؤ → و
        // ئ → ي
        $text = \str_replace(['أ', 'إ'], 'ا', $text);
        $text = \str_replace('ؤ', 'و', $text);
        $text = \str_replace('ئ', 'ي', $text);

        return $text;
    }

    /**
     * Remove punctuation
     */
    public function removePunctuation(string $text): string
    {
        $punctuation = ['،', '؛', '؟', '.', ',', ';', ':', '!', '?', '"', "'", '«', '»', '(', ')', '[', ']', '-'];

        return \str_replace($punctuation, '', $text);
    }

    /**
     * Keep only Arabic letters
     */
    public function keepOnlyLetters(string $text): string
    {
        return \preg_replace('/[^' . Config::ARABIC_RANGE . ']/u', '', $text) ?? $text;
    }

    /**
     * Light normalization (minimal changes)
     */
    public function normalizeLight(string $text): string
    {
        $text = $this->normalizeWhitespace($text);
        $text = $this->removeTatweel($text);

        return $text;
    }

    /**
     * Heavy normalization (maximum changes)
     */
    public function normalizeHeavy(string $text): string
    {
        $text = $this->normalize($text);
        $text = $this->normalizeWaw($text);
        $text = $this->normalizeYaa($text);
        $text = $this->removeHamza($text);
        $text = $this->removePunctuation($text);

        return $text;
    }

    /**
     * Check if text is normalized
     */
    public function isNormalized(string $text): bool
    {
        return $text === $this->normalize($text);
    }
}
