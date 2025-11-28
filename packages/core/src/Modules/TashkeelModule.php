<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Arabic;
use ArPHP\Core\Contracts\ServiceInterface;

/**
 * Tashkeel Service - Arabic Diacritics Processing
 * 
 * Handles adding and removing Arabic diacritics (tashkeel/harakat)
 */
class TashkeelService implements ServiceInterface
{
    /**
     * Arabic diacritics characters
     */
    private const TASHKEEL_CHARS = [
        "\u{064B}", // Fathatan ً
        "\u{064C}", // Dammatan ٌ
        "\u{064D}", // Kasratan ٍ
        "\u{064E}", // Fatha َ
        "\u{064F}", // Damma ُ
        "\u{0650}", // Kasra ِ
        "\u{0651}", // Shadda ّ
        "\u{0652}", // Sukun ْ
        "\u{0653}", // Maddah ٓ
        "\u{0654}", // Hamza Above ٔ
        "\u{0655}", // Hamza Below ٕ
        "\u{0656}", // Subscript Alef ٖ
        "\u{0640}", // Tatweel ـ
    ];

    /**
     * Remove all tashkeel from Arabic text
     */
    public function remove(string $text): string
    {
        $pattern = '/[' . implode('', self::TASHKEEL_CHARS) . ']/u';
        return preg_replace($pattern, '', $text) ?? $text;
    }

    /**
     * Check if text contains tashkeel
     */
    public function has(string $text): bool
    {
        $pattern = '/[' . implode('', self::TASHKEEL_CHARS) . ']/u';
        return preg_match($pattern, $text) === 1;
    }

    /**
     * Get all tashkeel marks from text
     * 
     * @return array<int, string>
     */
    public function extract(string $text): array
    {
        $pattern = '/[' . implode('', self::TASHKEEL_CHARS) . ']/u';
        preg_match_all($pattern, $text, $matches);
        return $matches[0];
    }

    /**
     * Count tashkeel marks in text
     */
    public function count(string $text): int
    {
        return count($this->extract($text));
    }

    /**
     * Normalize Arabic text (remove tashkeel and normalize forms)
     */
    public function normalize(string $text): string
    {
        // Remove tashkeel
        $text = $this->remove($text);
        
        // Normalize Alef variations to plain Alef
        $text = str_replace(['أ', 'إ', 'آ', 'ٱ'], 'ا', $text);
        
        // Normalize Teh Marbuta
        $text = str_replace('ة', 'ه', $text);
        
        // Normalize Yeh variations
        $text = str_replace(['ى', 'ئ'], 'ي', $text);
        
        return $text;
    }

    /**
     * Add common tashkeel patterns (basic implementation)
     * 
     * This is a simplified version. Full implementation would require
     * dictionary lookup or ML model.
     */
    public function add(string $text): string
    {
        // Basic patterns for common words
        $patterns = [
            'الله' => 'اللَّه',
            'محمد' => 'مُحَمَّد',
            'السلام' => 'السَّلام',
            'عليكم' => 'عَلَيْكُم',
            'مرحبا' => 'مَرْحَبًا',
            'شكرا' => 'شُكْرًا',
            'الحمد' => 'الحَمْد',
            'رمضان' => 'رَمَضَان',
        ];

        foreach ($patterns as $plain => $tashkeel) {
            $text = str_replace($plain, $tashkeel, $text);
        }

        return $text;
    }

    /**
     * Remove tashkeel from multiple texts (batch processing)
     * 
     * @param array<int, string> $texts
     * @return array<int, string>
     */
    public function removeBatch(array $texts): array
    {
        return array_map(fn($text) => $this->remove($text), $texts);
    }

    /**
     * Normalize multiple texts (batch processing)
     * 
     * @param array<int, string> $texts
     * @return array<int, string>
     */
    public function normalizeBatch(array $texts): array
    {
        return array_map(fn($text) => $this->normalize($text), $texts);
    }

    public function getServiceName(): string
    {
        return 'tashkeel';
    }

    public function getConfig(): array
    {
        return [
            'version' => '1.0.0',
            'features' => ['remove', 'removeBatch', 'has', 'extract', 'count', 'normalize', 'normalizeBatch', 'add'],
        ];
    }

    public function isAvailable(): bool
    {
        return extension_loaded('mbstring');
    }
}

/**
 * Tashkeel Module
 * 
 * Registers the Tashkeel service for Arabic diacritics processing
 */
class TashkeelModule extends AbstractModule
{
    protected string $version = '1.0.0';

    public function getName(): string
    {
        return 'tashkeel';
    }

    public function register(): void
    {
        Arabic::container()->register('tashkeel', function () {
            return new TashkeelService();
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
