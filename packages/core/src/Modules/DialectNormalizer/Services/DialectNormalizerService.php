<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\DialectNormalizer\Services;

use ArPHP\Core\Modules\DialectNormalizer\Config;
use ArPHP\Core\Modules\DialectNormalizer\Contracts\DialectNormalizerInterface;
use ArPHP\Core\Modules\DialectNormalizer\Exceptions\DialectNormalizerException;

/**
 * DialectNormalizer Service - PHP 8.4
 *
 * @package ArPHP\Core\Modules\DialectNormalizer
 */
final class DialectNormalizerService implements DialectNormalizerInterface
{
    /** @var array<string, array<string, string>> */
    private array $dialectMappings = [];

    /** @var array<string, array<string>> */
    private array $dialectMarkers = [];

    public function __construct()
    {
        $this->loadMappings();
    }

    /**
     * Load dialect mappings
     */
    private function loadMappings(): void
    {
        $this->dialectMappings = [
            Config::DIALECT_EGYPTIAN => Config::EGYPTIAN_TO_MSA,
            Config::DIALECT_LEVANTINE => Config::LEVANTINE_TO_MSA,
            Config::DIALECT_GULF => Config::GULF_TO_MSA,
            Config::DIALECT_MAGHREBI => Config::MAGHREBI_TO_MSA,
            Config::DIALECT_IRAQI => Config::IRAQI_TO_MSA,
        ];

        $this->dialectMarkers = [
            Config::DIALECT_EGYPTIAN => Config::EGYPTIAN_MARKERS,
            Config::DIALECT_LEVANTINE => Config::LEVANTINE_MARKERS,
            Config::DIALECT_GULF => Config::GULF_MARKERS,
            Config::DIALECT_MAGHREBI => Config::MAGHREBI_MARKERS,
            Config::DIALECT_IRAQI => Config::IRAQI_MARKERS,
        ];
    }

    /**
     * @inheritDoc
     */
    public function normalize(string $text): string
    {
        // Detect dialect first
        $dialect = $this->detectDialect($text);

        if ($dialect === Config::DIALECT_MSA) {
            return $text;
        }

        return $this->normalizeDialect($text, $dialect);
    }

    /**
     * @inheritDoc
     */
    public function normalizeDialect(string $text, string $dialect): string
    {
        if (!\in_array($dialect, Config::SUPPORTED_DIALECTS, true)) {
            throw DialectNormalizerException::unsupportedDialect($dialect);
        }

        if ($dialect === Config::DIALECT_MSA) {
            return $text;
        }

        $mappings = $this->dialectMappings[$dialect] ?? [];

        // Sort by length (longest first) to avoid partial replacements
        \uksort($mappings, fn($a, $b) => \mb_strlen($b) - \mb_strlen($a));

        foreach ($mappings as $dialectal => $standard) {
            $text = $this->replaceWord($text, $dialectal, $standard);
        }

        return $text;
    }

    /**
     * Replace word with word boundaries
     */
    private function replaceWord(string $text, string $search, string $replace): string
    {
        // Pattern to match word boundaries in Arabic
        $pattern = '/(?<=^|[\s،؛:.!?؟\-\(\)\[\]])' . \preg_quote($search, '/') . '(?=$|[\s،؛:.!?؟\-\(\)\[\]])/u';

        return \preg_replace($pattern, $replace, $text) ?? $text;
    }

    /**
     * @inheritDoc
     */
    public function detectDialect(string $text): string
    {
        $scores = $this->getDialectScores($text);

        // Find highest score
        $maxScore = 0.0;
        $detectedDialect = Config::DIALECT_MSA;

        foreach ($scores as $dialect => $score) {
            if ($score > $maxScore) {
                $maxScore = $score;
                $detectedDialect = $dialect;
            }
        }

        // If no clear dialect markers, assume MSA
        if ($maxScore < 0.1) {
            return Config::DIALECT_MSA;
        }

        return $detectedDialect;
    }

    /**
     * @inheritDoc
     */
    public function getDialectScores(string $text): array
    {
        $text = $this->normalizeForComparison($text);
        $words = $this->tokenize($text);
        $totalWords = \count($words);

        if ($totalWords === 0) {
            return \array_fill_keys(Config::SUPPORTED_DIALECTS, 0.0);
        }

        $scores = [];

        foreach ($this->dialectMarkers as $dialect => $markers) {
            $matchCount = 0;

            foreach ($markers as $marker) {
                $normalizedMarker = $this->normalizeForComparison($marker);
                foreach ($words as $word) {
                    if ($word === $normalizedMarker || \mb_strpos($word, $normalizedMarker) !== false) {
                        ++$matchCount;
                    }
                }
            }

            $scores[$dialect] = $matchCount / $totalWords;
        }

        // MSA score is inverse of dialectal markers
        $maxDialectalScore = \max($scores) ?: 0.0;
        $scores[Config::DIALECT_MSA] = 1.0 - $maxDialectalScore;

        return $scores;
    }

    /**
     * Normalize text for comparison
     */
    private function normalizeForComparison(string $text): string
    {
        // Remove diacritics
        $diacritics = ['ً', 'ٌ', 'ٍ', 'َ', 'ُ', 'ِ', 'ّ', 'ْ', 'ـ'];
        $text = \str_replace($diacritics, '', $text);

        // Normalize Alef
        $text = \str_replace(['أ', 'إ', 'آ', 'ٱ'], 'ا', $text);

        return \trim($text);
    }

    /**
     * @inheritDoc
     */
    public function convert(string $text, string $fromDialect, string $toDialect): string
    {
        if ($fromDialect === $toDialect) {
            return $text;
        }

        if (!\in_array($fromDialect, Config::SUPPORTED_DIALECTS, true)) {
            throw DialectNormalizerException::unsupportedDialect($fromDialect);
        }

        if (!\in_array($toDialect, Config::SUPPORTED_DIALECTS, true)) {
            throw DialectNormalizerException::unsupportedDialect($toDialect);
        }

        // First normalize to MSA
        $msaText = $fromDialect === Config::DIALECT_MSA
            ? $text
            : $this->normalizeDialect($text, $fromDialect);

        // If target is MSA, we're done
        if ($toDialect === Config::DIALECT_MSA) {
            return $msaText;
        }

        // Convert from MSA to target dialect (reverse mapping)
        $reverseMappings = $this->getReverseMappings($toDialect);

        foreach ($reverseMappings as $standard => $dialectal) {
            $msaText = $this->replaceWord($msaText, $standard, $dialectal);
        }

        return $msaText;
    }

    /**
     * Get reverse mappings (MSA to dialect)
     *
     * @return array<string, string>
     */
    private function getReverseMappings(string $dialect): array
    {
        $mappings = $this->dialectMappings[$dialect] ?? [];

        return \array_flip($mappings);
    }

    /**
     * @inheritDoc
     */
    public function isMSA(string $text): bool
    {
        $scores = $this->getDialectScores($text);

        // Consider MSA if MSA score is highest and dialectal markers are low
        $msaScore = $scores[Config::DIALECT_MSA] ?? 0.0;
        unset($scores[Config::DIALECT_MSA]);

        $maxDialectalScore = \max($scores) ?: 0.0;

        return $msaScore > $maxDialectalScore && $maxDialectalScore < 0.1;
    }

    /**
     * @inheritDoc
     */
    public function getSupportedDialects(): array
    {
        return Config::SUPPORTED_DIALECTS;
    }

    /**
     * Tokenize text
     *
     * @return array<string>
     */
    private function tokenize(string $text): array
    {
        $words = \preg_split('/[\s،؛:.!?؟\-\(\)\[\]«»"]+/u', $text, -1, \PREG_SPLIT_NO_EMPTY);

        return $words !== false ? $words : [];
    }

    /**
     * Get dialect name
     */
    public function getDialectName(string $code): string
    {
        return Config::DIALECT_NAMES[$code] ?? 'Unknown';
    }

    /**
     * Normalize all dialects
     */
    public function normalizeAll(string $text): string
    {
        // Apply all dialect normalizations
        foreach ($this->dialectMappings as $mappings) {
            foreach ($mappings as $dialectal => $standard) {
                $text = $this->replaceWord($text, $dialectal, $standard);
            }
        }

        return $text;
    }

    /**
     * Extract dialectal words from text
     *
     * @return array<string>
     */
    public function extractDialectalWords(string $text): array
    {
        $text = $this->normalizeForComparison($text);
        $words = $this->tokenize($text);
        $dialectalWords = [];

        foreach ($words as $word) {
            foreach ($this->dialectMarkers as $markers) {
                if (\in_array($word, $markers, true)) {
                    $dialectalWords[] = $word;
                    break;
                }
            }
        }

        return \array_unique($dialectalWords);
    }

    /**
     * Check if text contains dialectal words
     */
    public function hasDialectalWords(string $text): bool
    {
        return \count($this->extractDialectalWords($text)) > 0;
    }

    /**
     * Get dialectal word count
     */
    public function countDialectalWords(string $text): int
    {
        return \count($this->extractDialectalWords($text));
    }
}
