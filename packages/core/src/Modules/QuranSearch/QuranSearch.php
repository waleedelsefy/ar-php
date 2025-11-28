<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\QuranSearch;

/**
 * QuranSearch Facade - PHP 8.4
 *
 * Static facade for easy access to Quran search functionality.
 *
 * Usage:
 *   use ArPHP\Core\Modules\QuranSearch\QuranSearch;
 *
 *   $results = QuranSearch::search('الله');
 *   $verse = QuranSearch::getVerse(1, 1);
 *   $fatiha = QuranSearch::getSurah(1);
 *
 * @package ArPHP\Core\Modules\QuranSearch
 */
final class QuranSearch
{
    private static ?QuranSearchModule $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): QuranSearchModule
    {
        if (self::$instance === null) {
            self::$instance = new QuranSearchModule();
            self::$instance->register();
        }

        return self::$instance;
    }

    /**
     * Configure the module
     *
     * @param array{data_path?: string} $config
     */
    public static function configure(array $config): QuranSearchModule
    {
        self::$instance = new QuranSearchModule($config);
        self::$instance->register();

        return self::$instance;
    }

    /**
     * Reset the singleton instance
     */
    public static function reset(): void
    {
        self::$instance = null;
    }

    /**
     * Search in Quran text
     *
     * Example:
     *   QuranSearch::search('الرحمن')
     *   QuranSearch::search('الرحمن', fuzzy: true)
     *
     * @return array<array{surah: int, ayah: int, text: string, match: string, position: int}>
     */
    public static function search(string $query, bool $fuzzy = false): array
    {
        return self::getInstance()->search($query, $fuzzy);
    }

    /**
     * Get a specific verse
     *
     * Example:
     *   QuranSearch::getVerse(1, 1)
     *   // ['surah' => 1, 'ayah' => 1, 'text' => 'بسم الله...', 'surah_name' => 'الفاتحة']
     *
     * @return array{surah: int, ayah: int, text: string, surah_name: string}|null
     */
    public static function getVerse(int $surah, int $ayah): ?array
    {
        return self::getInstance()->getVerse($surah, $ayah);
    }

    /**
     * Get verse text only
     */
    public static function verse(int $surah, int $ayah): ?string
    {
        $verse = self::getVerse($surah, $ayah);

        return $verse['text'] ?? null;
    }

    /**
     * Get range of verses
     *
     * Example:
     *   QuranSearch::getVerses(1, 1, 3) // First 3 verses of Al-Fatiha
     *
     * @return array<array{surah: int, ayah: int, text: string}>
     */
    public static function getVerses(int $surah, int $fromAyah, int $toAyah): array
    {
        return self::getInstance()->getVerses($surah, $fromAyah, $toAyah);
    }

    /**
     * Get entire surah
     *
     * Example:
     *   QuranSearch::getSurah(112) // Surah Al-Ikhlas
     *
     * @return array{name: string, verses: array<array{ayah: int, text: string}>}|null
     */
    public static function getSurah(int $surah): ?array
    {
        return self::getInstance()->getSurah($surah);
    }

    /**
     * Get surah name
     *
     * Example:
     *   QuranSearch::getSurahName(1) // "الفاتحة"
     */
    public static function getSurahName(int $surah): ?string
    {
        return self::getInstance()->getSurahName($surah);
    }

    /**
     * Get ayah count for surah
     *
     * Example:
     *   QuranSearch::getAyahCount(1) // 7
     */
    public static function getAyahCount(int $surah): int
    {
        return self::getInstance()->getAyahCount($surah);
    }

    /**
     * Search by Arabic root
     *
     * Example:
     *   QuranSearch::searchByRoot('رحم') // Find all words from root ر-ح-م
     *
     * @return array<array{surah: int, ayah: int, text: string, words: array<string>}>
     */
    public static function searchByRoot(string $root): array
    {
        return self::getInstance()->searchByRoot($root);
    }

    /**
     * Get word frequency in Quran
     *
     * Example:
     *   QuranSearch::wordFrequency('الله') // Number of occurrences
     */
    public static function wordFrequency(string $word): int
    {
        return self::getInstance()->wordFrequency($word);
    }

    /**
     * Find similar verses
     *
     * @return array<array{surah: int, ayah: int, text: string, similarity: float}>
     */
    public static function findSimilar(string $text, int $limit = 10): array
    {
        return self::getInstance()->findSimilar($text, $limit);
    }

    /**
     * Get all surah names
     *
     * @return array<int, string>
     */
    public static function getAllSurahNames(): array
    {
        return self::getInstance()->getAllSurahNames();
    }

    /**
     * Get Quran reference string
     *
     * Example:
     *   QuranSearch::reference(1, 1) // "الفاتحة: 1"
     */
    public static function reference(int $surah, int $ayah): string
    {
        $surahName = self::getSurahName($surah);

        return "{$surahName}: {$ayah}";
    }

    /**
     * Get Bismillah
     */
    public static function bismillah(): string
    {
        return self::verse(1, 1) ?? 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ';
    }

    /**
     * Get Al-Fatiha
     *
     * @return array<array{ayah: int, text: string}>
     */
    public static function fatiha(): array
    {
        $surah = self::getSurah(1);

        return $surah['verses'] ?? [];
    }

    /**
     * Get Al-Ikhlas
     *
     * @return array<array{ayah: int, text: string}>
     */
    public static function ikhlas(): array
    {
        $surah = self::getSurah(112);

        return $surah['verses'] ?? [];
    }

    /**
     * Check if verse reference is valid
     */
    public static function isValidReference(int $surah, int $ayah): bool
    {
        if ($surah < 1 || $surah > 114) {
            return false;
        }

        $maxAyah = self::getAyahCount($surah);

        return $ayah >= 1 && $ayah <= $maxAyah;
    }

    /**
     * Get random verse
     *
     * @return array{surah: int, ayah: int, text: string, surah_name: string}|null
     */
    public static function randomVerse(): ?array
    {
        $surah = \random_int(1, 114);
        $maxAyah = self::getAyahCount($surah);

        if ($maxAyah === 0) {
            return null;
        }

        $ayah = \random_int(1, $maxAyah);

        return self::getVerse($surah, $ayah);
    }
}
