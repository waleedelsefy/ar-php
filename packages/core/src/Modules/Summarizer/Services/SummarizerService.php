<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Summarizer\Services;

use ArPHP\Core\Modules\Summarizer\Contracts\SummarizerInterface;
use ArPHP\Core\Modules\Summarizer\Config;
use ArPHP\Core\Modules\Summarizer\Exceptions\SummarizerException;

/**
 * Summarizer Service - PHP 8.4
 *
 * Arabic text summarization using extractive methods.
 *
 * @package ArPHP\Core\Modules\Summarizer
 */
final class SummarizerService implements SummarizerInterface
{
    /**
     * Constructor
     */
    public function __construct() {}

    /**
     * {@inheritdoc}
     */
    public function summarize(string $text, int $numSentences = 3): string
    {
        $sentences = $this->splitSentences($text);

        if (count($sentences) === 0) {
            return $text;
        }

        if (count($sentences) <= $numSentences) {
            return $text;
        }

        $keySentences = $this->extractKeySentences($text, $numSentences);

        // Sort by original position to maintain flow
        usort($keySentences, fn($a, $b) => $a['position'] <=> $b['position']);

        return implode(' ', array_column($keySentences, 'sentence'));
    }

    /**
     * {@inheritdoc}
     */
    public function summarizeByRatio(string $text, float $ratio = 0.3): string
    {
        if ($ratio <= 0.0 || $ratio > 1.0) {
            throw SummarizerException::invalidRatio($ratio);
        }

        $sentences = $this->splitSentences($text);
        $numSentences = max(1, (int) ceil(count($sentences) * $ratio));

        return $this->summarize($text, $numSentences);
    }

    /**
     * {@inheritdoc}
     */
    public function extractKeySentences(string $text, int $count = 5): array
    {
        $scoredSentences = $this->scoreSentences($text);

        if (empty($scoredSentences)) {
            return [];
        }

        // Sort by score descending
        usort($scoredSentences, fn($a, $b) => $b['score'] <=> $a['score']);

        // Get top sentences
        $result = [];
        $taken = 0;

        foreach ($scoredSentences as $index => $data) {
            if ($taken >= $count) {
                break;
            }

            $result[] = [
                'sentence' => $data['sentence'],
                'score' => $data['score'],
                'position' => $data['position'] ?? $index,
            ];
            $taken++;
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function scoreSentences(string $text): array
    {
        $sentences = $this->splitSentences($text);

        if (empty($sentences)) {
            return [];
        }

        // Extract keywords with their frequencies
        $keywords = $this->extractKeywords($text, 20);

        $totalSentences = count($sentences);
        $result = [];

        foreach ($sentences as $position => $sentence) {
            $score = $this->calculateSentenceScore(
                $sentence,
                $keywords,
                $position,
                $totalSentences
            );

            $result[] = [
                'sentence' => $sentence,
                'score' => round($score, 4),
                'position' => $position,
            ];
        }

        return $result;
    }

    /**
     * {@inheritdoc}
     */
    public function extractKeywords(string $text, int $count = 10): array
    {
        // Normalize text
        $normalized = $this->normalizeText($text);

        // Tokenize
        $words = preg_split('/\s+/u', $normalized, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        // Filter stopwords and short words
        $filteredWords = array_filter($words, function ($word) {
            return mb_strlen($word) > 2 && !in_array($word, Config::STOPWORDS, true);
        });

        // Count frequencies
        $frequencies = array_count_values($filteredWords);

        // Calculate TF-IDF-like scores
        $totalWords = count($filteredWords);
        $scores = [];

        foreach ($frequencies as $word => $freq) {
            // TF: term frequency normalized
            $tf = $freq / $totalWords;
            // Simple IDF approximation based on document frequency
            $idf = log(1 + $totalWords / $freq);
            $scores[$word] = $tf * $idf;
        }

        // Sort by score
        arsort($scores);

        return array_slice($scores, 0, $count, true);
    }

    /**
     * Split text into sentences
     *
     * @return array<string>
     */
    public function splitSentences(string $text): array
    {
        $sentences = preg_split(Config::SENTENCE_PATTERN, $text, -1, PREG_SPLIT_NO_EMPTY);

        if ($sentences === false) {
            return [];
        }

        // Clean and filter sentences
        return array_values(array_filter(
            array_map(fn($s) => trim($s), $sentences),
            fn($s) => mb_strlen($s) > 10
        ));
    }

    /**
     * Calculate sentence importance score
     *
     * @param array<string, float> $keywords
     */
    private function calculateSentenceScore(
        string $sentence,
        array $keywords,
        int $position,
        int $totalSentences
    ): float {
        $score = 0.0;
        $normalizedSentence = $this->normalizeText($sentence);
        $words = preg_split('/\s+/u', $normalizedSentence, -1, PREG_SPLIT_NO_EMPTY) ?: [];

        if (empty($words)) {
            return 0.0;
        }

        // Keyword score
        foreach ($words as $word) {
            if (isset($keywords[$word])) {
                $score += $keywords[$word] * Config::KEYWORD_WEIGHT;
            }
        }

        // Normalize by sentence length
        $score /= count($words);

        // Position bonus
        if ($position === 0) {
            $score *= Config::POSITION_WEIGHT_FIRST;
        } elseif ($position === $totalSentences - 1) {
            $score *= Config::POSITION_WEIGHT_LAST;
        } elseif ($position < 3) {
            $score *= 1.3; // Early sentences bonus
        }

        // Length factor (prefer medium-length sentences)
        $sentenceLength = count($words);
        if ($sentenceLength >= 10 && $sentenceLength <= 30) {
            $score *= 1.2;
        } elseif ($sentenceLength > 50) {
            $score *= 0.8;
        }

        return $score;
    }

    /**
     * Normalize text for processing
     */
    private function normalizeText(string $text): string
    {
        // Remove diacritics
        $text = str_replace(Config::DIACRITICS, '', $text);

        // Normalize Alef variations
        $text = preg_replace('/[أإآٱ]/u', 'ا', $text) ?? $text;

        // Normalize Yeh
        $text = str_replace('ى', 'ي', $text);

        // Normalize Teh Marbuta
        $text = str_replace('ة', 'ه', $text);

        // Remove punctuation (keep Arabic letters and spaces)
        $text = preg_replace('/[^\p{Arabic}\s]/u', ' ', $text) ?? $text;

        // Collapse whitespace
        $text = preg_replace('/\s+/u', ' ', $text) ?? $text;

        return trim($text);
    }

    /**
     * Get text statistics
     *
     * @return array<string, mixed>
     */
    public function getTextStatistics(string $text): array
    {
        $sentences = $this->splitSentences($text);
        $words = preg_split('/\s+/u', $this->normalizeText($text), -1, PREG_SPLIT_NO_EMPTY) ?: [];

        return [
            'total_characters' => mb_strlen($text),
            'total_words' => count($words),
            'total_sentences' => count($sentences),
            'avg_words_per_sentence' => count($sentences) > 0 
                ? round(count($words) / count($sentences), 2) 
                : 0,
            'avg_chars_per_word' => count($words) > 0 
                ? round(mb_strlen(implode('', $words)) / count($words), 2) 
                : 0,
        ];
    }

    /**
     * Get headline from text (first key sentence, truncated)
     */
    public function generateHeadline(string $text, int $maxLength = 100): string
    {
        $keySentences = $this->extractKeySentences($text, 1);

        if (empty($keySentences)) {
            return mb_substr($text, 0, $maxLength);
        }

        $headline = $keySentences[0]['sentence'];

        if (mb_strlen($headline) > $maxLength) {
            $headline = mb_substr($headline, 0, $maxLength - 3) . '...';
        }

        return $headline;
    }
}
