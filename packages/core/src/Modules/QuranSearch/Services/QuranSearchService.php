<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\QuranSearch\Services;

use ArPHP\Core\Modules\QuranSearch\Config;
use ArPHP\Core\Modules\QuranSearch\Contracts\QuranSearchInterface;
use ArPHP\Core\Modules\QuranSearch\Exceptions\QuranSearchException;

/**
 * Quran Search Service - PHP 8.4
 *
 * @package ArPHP\Core\Modules\QuranSearch
 */
final class QuranSearchService implements QuranSearchInterface
{
    /** @var array<int, array<int, string>> */
    private array $quranData = [];

    private bool $dataLoaded = false;

    public function __construct(
        private readonly ?string $dataPath = null
    ) {
    }

    /**
     * @inheritDoc
     */
    public function search(string $query, bool $fuzzy = false): array
    {
        $query = \trim($query);

        if ($query === '') {
            throw QuranSearchException::emptyQuery();
        }

        $this->ensureDataLoaded();

        $normalizedQuery = $this->normalizeForSearch($query);
        $results = [];

        foreach ($this->quranData as $surah => $ayahs) {
            foreach ($ayahs as $ayah => $text) {
                $normalizedText = $this->normalizeForSearch($text);

                if ($fuzzy) {
                    $match = $this->fuzzyMatch($normalizedQuery, $normalizedText);
                } else {
                    $match = \mb_strpos($normalizedText, $normalizedQuery);
                }

                if ($match !== false) {
                    $results[] = [
                        'surah' => $surah,
                        'ayah' => $ayah,
                        'text' => $text,
                        'match' => $query,
                        'position' => \is_int($match) ? $match : 0,
                    ];

                    if (\count($results) >= Config::DEFAULT_MAX_RESULTS) {
                        break 2;
                    }
                }
            }
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function getVerse(int $surah, int $ayah): ?array
    {
        $this->validateSurah($surah);
        $this->validateAyah($surah, $ayah);

        $this->ensureDataLoaded();

        if (!isset($this->quranData[$surah][$ayah])) {
            return null;
        }

        return [
            'surah' => $surah,
            'ayah' => $ayah,
            'text' => $this->quranData[$surah][$ayah],
            'surah_name' => Config::SURAH_NAMES[$surah],
        ];
    }

    /**
     * @inheritDoc
     */
    public function getVerses(int $surah, int $fromAyah, int $toAyah): array
    {
        $this->validateSurah($surah);

        if ($fromAyah > $toAyah) {
            throw QuranSearchException::invalidRange($fromAyah, $toAyah);
        }

        $this->ensureDataLoaded();

        $results = [];

        for ($ayah = $fromAyah; $ayah <= $toAyah; $ayah++) {
            if (isset($this->quranData[$surah][$ayah])) {
                $results[] = [
                    'surah' => $surah,
                    'ayah' => $ayah,
                    'text' => $this->quranData[$surah][$ayah],
                ];
            }
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function getSurah(int $surah): ?array
    {
        $this->validateSurah($surah);
        $this->ensureDataLoaded();

        if (!isset($this->quranData[$surah])) {
            return null;
        }

        $verses = [];
        foreach ($this->quranData[$surah] as $ayah => $text) {
            $verses[] = [
                'ayah' => $ayah,
                'text' => $text,
            ];
        }

        return [
            'name' => Config::SURAH_NAMES[$surah],
            'verses' => $verses,
        ];
    }

    /**
     * @inheritDoc
     */
    public function getSurahName(int $surah): ?string
    {
        return Config::SURAH_NAMES[$surah] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function getAyahCount(int $surah): int
    {
        return Config::AYAH_COUNTS[$surah] ?? 0;
    }

    /**
     * @inheritDoc
     */
    public function searchByRoot(string $root): array
    {
        $root = \trim($root);

        if ($root === '') {
            throw QuranSearchException::emptyQuery();
        }

        $this->ensureDataLoaded();

        // Simple root-based search - matches words containing root letters in sequence
        $rootLetters = \preg_split('//u', $root, -1, PREG_SPLIT_NO_EMPTY);
        $pattern = \implode('.*?', \array_map(fn($l) => \preg_quote($l, '/'), $rootLetters));

        $results = [];

        foreach ($this->quranData as $surah => $ayahs) {
            foreach ($ayahs as $ayah => $text) {
                $normalizedText = $this->normalizeForSearch($text);
                $words = \preg_split('/\s+/u', $normalizedText, -1, PREG_SPLIT_NO_EMPTY);
                $matchedWords = [];

                foreach ($words as $word) {
                    if (\preg_match('/' . $pattern . '/u', $word)) {
                        $matchedWords[] = $word;
                    }
                }

                if (!empty($matchedWords)) {
                    $results[] = [
                        'surah' => $surah,
                        'ayah' => $ayah,
                        'text' => $text,
                        'words' => $matchedWords,
                    ];
                }
            }
        }

        return $results;
    }

    /**
     * @inheritDoc
     */
    public function normalizeForSearch(string $text): string
    {
        // Remove diacritics
        $text = \str_replace(Config::SEARCH_DIACRITICS, '', $text);

        // Normalize alef variants
        $text = \str_replace(Config::ALEF_VARIANTS, 'ا', $text);

        // Normalize ta marbuta
        $text = \str_replace('ة', 'ه', $text);

        // Normalize alef maqsura
        $text = \str_replace('ى', 'ي', $text);

        return \trim($text);
    }

    /**
     * @inheritDoc
     */
    public function wordFrequency(string $word): int
    {
        $this->ensureDataLoaded();

        $normalizedWord = $this->normalizeForSearch($word);
        $count = 0;

        foreach ($this->quranData as $ayahs) {
            foreach ($ayahs as $text) {
                $normalizedText = $this->normalizeForSearch($text);
                $count += \mb_substr_count($normalizedText, $normalizedWord);
            }
        }

        return $count;
    }

    /**
     * @inheritDoc
     */
    public function findSimilar(string $text, int $limit = 10): array
    {
        $this->ensureDataLoaded();

        $normalizedInput = $this->normalizeForSearch($text);
        $results = [];

        foreach ($this->quranData as $surah => $ayahs) {
            foreach ($ayahs as $ayah => $verseText) {
                $normalizedVerse = $this->normalizeForSearch($verseText);
                $similarity = $this->calculateSimilarity($normalizedInput, $normalizedVerse);

                if ($similarity > 0.3) { // Threshold
                    $results[] = [
                        'surah' => $surah,
                        'ayah' => $ayah,
                        'text' => $verseText,
                        'similarity' => $similarity,
                    ];
                }
            }
        }

        // Sort by similarity descending
        \usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        return \array_slice($results, 0, $limit);
    }

    /**
     * @inheritDoc
     */
    public function getAllSurahNames(): array
    {
        return Config::SURAH_NAMES;
    }

    /**
     * Load sample data for demonstration
     */
    public function loadSampleData(): void
    {
        // Sample verses for demonstration
        $this->quranData = [
            1 => [
                1 => 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ',
                2 => 'الْحَمْدُ لِلَّهِ رَبِّ الْعَالَمِينَ',
                3 => 'الرَّحْمَٰنِ الرَّحِيمِ',
                4 => 'مَالِكِ يَوْمِ الدِّينِ',
                5 => 'إِيَّاكَ نَعْبُدُ وَإِيَّاكَ نَسْتَعِينُ',
                6 => 'اهْدِنَا الصِّرَاطَ الْمُسْتَقِيمَ',
                7 => 'صِرَاطَ الَّذِينَ أَنْعَمْتَ عَلَيْهِمْ غَيْرِ الْمَغْضُوبِ عَلَيْهِمْ وَلَا الضَّالِّينَ',
            ],
            112 => [
                1 => 'قُلْ هُوَ اللَّهُ أَحَدٌ',
                2 => 'اللَّهُ الصَّمَدُ',
                3 => 'لَمْ يَلِدْ وَلَمْ يُولَدْ',
                4 => 'وَلَمْ يَكُن لَّهُ كُفُوًا أَحَدٌ',
            ],
            113 => [
                1 => 'قُلْ أَعُوذُ بِرَبِّ الْفَلَقِ',
                2 => 'مِن شَرِّ مَا خَلَقَ',
                3 => 'وَمِن شَرِّ غَاسِقٍ إِذَا وَقَبَ',
                4 => 'وَمِن شَرِّ النَّفَّاثَاتِ فِي الْعُقَدِ',
                5 => 'وَمِن شَرِّ حَاسِدٍ إِذَا حَسَدَ',
            ],
            114 => [
                1 => 'قُلْ أَعُوذُ بِرَبِّ النَّاسِ',
                2 => 'مَلِكِ النَّاسِ',
                3 => 'إِلَٰهِ النَّاسِ',
                4 => 'مِن شَرِّ الْوَسْوَاسِ الْخَنَّاسِ',
                5 => 'الَّذِي يُوَسْوِسُ فِي صُدُورِ النَّاسِ',
                6 => 'مِنَ الْجِنَّةِ وَالنَّاسِ',
            ],
        ];

        $this->dataLoaded = true;
    }

    /**
     * Ensure data is loaded
     */
    private function ensureDataLoaded(): void
    {
        if (!$this->dataLoaded) {
            $this->loadSampleData();
        }
    }

    /**
     * Validate surah number
     */
    private function validateSurah(int $surah): void
    {
        if ($surah < 1 || $surah > Config::TOTAL_SURAHS) {
            throw QuranSearchException::invalidSurah($surah);
        }
    }

    /**
     * Validate ayah number
     */
    private function validateAyah(int $surah, int $ayah): void
    {
        $maxAyah = Config::AYAH_COUNTS[$surah] ?? 0;

        if ($ayah < 1 || $ayah > $maxAyah) {
            throw QuranSearchException::invalidAyah($surah, $ayah);
        }
    }

    /**
     * Fuzzy match implementation
     */
    private function fuzzyMatch(string $query, string $text): int|false
    {
        // First try exact match
        $pos = \mb_strpos($text, $query);

        if ($pos !== false) {
            return $pos;
        }

        // Try with wildcards (each letter can have any diacritics)
        $pattern = '';
        $queryChars = \preg_split('//u', $query, -1, PREG_SPLIT_NO_EMPTY);

        foreach ($queryChars as $char) {
            if (\trim($char) !== '') {
                $pattern .= \preg_quote($char, '/') . '[\x{064B}-\x{0652}]*';
            }
        }

        if (\preg_match('/' . $pattern . '/u', $text, $matches, PREG_OFFSET_CAPTURE)) {
            return $matches[0][1];
        }

        return false;
    }

    /**
     * Calculate similarity between two strings
     */
    private function calculateSimilarity(string $str1, string $str2): float
    {
        $words1 = \array_unique(\preg_split('/\s+/u', $str1, -1, PREG_SPLIT_NO_EMPTY));
        $words2 = \array_unique(\preg_split('/\s+/u', $str2, -1, PREG_SPLIT_NO_EMPTY));

        $intersection = \count(\array_intersect($words1, $words2));
        $union = \count(\array_unique(\array_merge($words1, $words2)));

        if ($union === 0) {
            return 0.0;
        }

        return $intersection / $union;
    }
}
