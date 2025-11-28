<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\QuranSearch\Contracts;

/**
 * Quran Search Interface - PHP 8.4
 *
 * @package ArPHP\Core\Modules\QuranSearch
 */
interface QuranSearchInterface
{
    /**
     * Search in Quran text
     *
     * @return array<array{
     *     surah: int,
     *     ayah: int,
     *     text: string,
     *     match: string,
     *     position: int
     * }>
     */
    public function search(string $query, bool $fuzzy = false): array;

    /**
     * Get verse by reference
     *
     * @return array{surah: int, ayah: int, text: string, surah_name: string}|null
     */
    public function getVerse(int $surah, int $ayah): ?array;

    /**
     * Get range of verses
     *
     * @return array<array{surah: int, ayah: int, text: string}>
     */
    public function getVerses(int $surah, int $fromAyah, int $toAyah): array;

    /**
     * Get entire surah
     *
     * @return array{name: string, verses: array<array{ayah: int, text: string}>}|null
     */
    public function getSurah(int $surah): ?array;

    /**
     * Get surah name
     */
    public function getSurahName(int $surah): ?string;

    /**
     * Get total ayah count for surah
     */
    public function getAyahCount(int $surah): int;

    /**
     * Search with root word
     *
     * @return array<array{surah: int, ayah: int, text: string, words: array<string>}>
     */
    public function searchByRoot(string $root): array;

    /**
     * Normalize Quran text for search
     */
    public function normalizeForSearch(string $text): string;

    /**
     * Get word frequency in Quran
     */
    public function wordFrequency(string $word): int;

    /**
     * Get similar verses
     *
     * @return array<array{surah: int, ayah: int, text: string, similarity: float}>
     */
    public function findSimilar(string $text, int $limit = 10): array;

    /**
     * Get all surah names
     *
     * @return array<int, string>
     */
    public function getAllSurahNames(): array;
}
