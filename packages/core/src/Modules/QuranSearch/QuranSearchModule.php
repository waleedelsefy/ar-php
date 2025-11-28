<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\QuranSearch;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Modules\QuranSearch\Contracts\QuranSearchInterface;
use ArPHP\Core\Modules\QuranSearch\Services\QuranSearchService;

/**
 * Quran Search Module - PHP 8.4
 *
 * Provides Quran text search and retrieval functionality.
 *
 * @package ArPHP\Core\Modules\QuranSearch
 */
final class QuranSearchModule extends AbstractModule
{
    protected string $version = '1.0.0';

    /** @var array<string> */
    protected array $dependencies = [];

    private ?QuranSearchService $service = null;

    /**
     * @param array{data_path?: string} $config
     */
    public function __construct(
        private array $config = []
    ) {
    }

    public function getName(): string
    {
        return 'quran_search';
    }

    public function register(): void
    {
        $this->service = new QuranSearchService(
            $this->config['data_path'] ?? null
        );
    }

    public function boot(): void
    {
        // Module ready
    }

    public function getService(): QuranSearchInterface
    {
        if ($this->service === null) {
            $this->register();
        }

        return $this->service;
    }

    /**
     * Search in Quran text
     *
     * @return array<array{surah: int, ayah: int, text: string, match: string, position: int}>
     */
    public function search(string $query, bool $fuzzy = false): array
    {
        return $this->getService()->search($query, $fuzzy);
    }

    /**
     * Get verse by reference
     *
     * @return array{surah: int, ayah: int, text: string, surah_name: string}|null
     */
    public function getVerse(int $surah, int $ayah): ?array
    {
        return $this->getService()->getVerse($surah, $ayah);
    }

    /**
     * Get range of verses
     *
     * @return array<array{surah: int, ayah: int, text: string}>
     */
    public function getVerses(int $surah, int $fromAyah, int $toAyah): array
    {
        return $this->getService()->getVerses($surah, $fromAyah, $toAyah);
    }

    /**
     * Get entire surah
     *
     * @return array{name: string, verses: array<array{ayah: int, text: string}>}|null
     */
    public function getSurah(int $surah): ?array
    {
        return $this->getService()->getSurah($surah);
    }

    /**
     * Get surah name
     */
    public function getSurahName(int $surah): ?string
    {
        return $this->getService()->getSurahName($surah);
    }

    /**
     * Get ayah count for surah
     */
    public function getAyahCount(int $surah): int
    {
        return $this->getService()->getAyahCount($surah);
    }

    /**
     * Search by Arabic root
     *
     * @return array<array{surah: int, ayah: int, text: string, words: array<string>}>
     */
    public function searchByRoot(string $root): array
    {
        return $this->getService()->searchByRoot($root);
    }

    /**
     * Get word frequency
     */
    public function wordFrequency(string $word): int
    {
        return $this->getService()->wordFrequency($word);
    }

    /**
     * Find similar verses
     *
     * @return array<array{surah: int, ayah: int, text: string, similarity: float}>
     */
    public function findSimilar(string $text, int $limit = 10): array
    {
        return $this->getService()->findSimilar($text, $limit);
    }

    /**
     * Get all surah names
     *
     * @return array<int, string>
     */
    public function getAllSurahNames(): array
    {
        return $this->getService()->getAllSurahNames();
    }

    public static function getIdentifier(): string
    {
        return 'quran_search';
    }

    /**
     * @return array<string>
     */
    public static function getPublicApi(): array
    {
        return [
            'search',
            'getVerse',
            'getVerses',
            'getSurah',
            'getSurahName',
            'getAyahCount',
            'searchByRoot',
            'wordFrequency',
            'findSimilar',
            'getAllSurahNames',
        ];
    }
}
