<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Sentiment\Services;

use ArPHP\Core\Modules\Sentiment\Config;
use ArPHP\Core\Modules\Sentiment\Contracts\SentimentInterface;

/**
 * Sentiment Service - PHP 8.4
 *
 * Arabic sentiment analysis with lexicon-based approach.
 *
 * @package ArPHP\Core\Modules\Sentiment
 */
final class SentimentService implements SentimentInterface
{
    /** @var array<string, float> */
    private array $positiveWords = [];

    /** @var array<string, float> */
    private array $negativeWords = [];

    /** @var array<string, float> */
    private array $intensifiers = [];

    /** @var array<string> */
    private array $negators = [];

    public function __construct()
    {
        $this->loadLexicon();
    }

    /**
     * Load sentiment lexicon
     */
    private function loadLexicon(): void
    {
        // Load and normalize positive words
        foreach (Config::POSITIVE_WORDS as $word => $weight) {
            $normalized = $this->normalizeWord($word);
            $this->positiveWords[$normalized] = $weight;
        }

        // Load and normalize negative words
        foreach (Config::NEGATIVE_WORDS as $word => $weight) {
            $normalized = $this->normalizeWord($word);
            $this->negativeWords[$normalized] = $weight;
        }

        // Load intensifiers
        foreach (Config::INTENSIFIERS as $word => $multiplier) {
            $normalized = $this->normalizeWord($word);
            $this->intensifiers[$normalized] = $multiplier;
        }

        // Load negators
        foreach (Config::NEGATORS as $negator) {
            $this->negators[] = $this->normalizeWord($negator);
        }
    }

    /**
     * Normalize word for comparison
     */
    private function normalizeWord(string $word): string
    {
        // Remove diacritics
        $word = \str_replace(Config::DIACRITICS, '', $word);

        // Normalize Alef
        $word = \str_replace(['أ', 'إ', 'آ', 'ٱ'], 'ا', $word);

        // Normalize Ta Marbuta
        $word = \str_replace('ة', 'ه', $word);

        // Normalize Alef Maqsura
        $word = \str_replace('ى', 'ي', $word);

        return \trim($word);
    }

    /**
     * @inheritDoc
     */
    public function analyze(string $text): array
    {
        $score = $this->getScore($text);
        $label = $this->getLabelFromScore($score);

        return [
            'score' => $score,
            'label' => $label,
            'confidence' => $this->calculateConfidence($text, $score),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getScore(string $text): float
    {
        $words = $this->tokenize($text);

        if (empty($words)) {
            return 0.0;
        }

        $totalScore = 0.0;
        $wordCount = 0;
        $negationActive = false;
        $intensifierMultiplier = 1.0;

        for ($i = 0; $i < \count($words); $i++) {
            $word = $words[$i];
            $normalized = $this->normalizeWord($word);

            // Check for negation
            if (\in_array($normalized, $this->negators, true)) {
                $negationActive = true;
                continue;
            }

            // Check for intensifier
            if (isset($this->intensifiers[$normalized])) {
                $intensifierMultiplier = $this->intensifiers[$normalized];
                continue;
            }

            // Check sentiment
            $wordScore = 0.0;

            if (isset($this->positiveWords[$normalized])) {
                $wordScore = $this->positiveWords[$normalized];
            } elseif (isset($this->negativeWords[$normalized])) {
                $wordScore = $this->negativeWords[$normalized];
            }

            if ($wordScore !== 0.0) {
                // Apply negation
                if ($negationActive) {
                    $wordScore *= -1;
                    $negationActive = false;
                }

                // Apply intensifier
                $wordScore *= $intensifierMultiplier;
                $intensifierMultiplier = 1.0;

                $totalScore += $wordScore;
                ++$wordCount;
            } else {
                // Reset modifiers after neutral word
                $negationActive = false;
                $intensifierMultiplier = 1.0;
            }
        }

        // Average score
        if ($wordCount > 0) {
            return \max(-1.0, \min(1.0, $totalScore / $wordCount));
        }

        return 0.0;
    }

    /**
     * @inheritDoc
     */
    public function getLabel(string $text): string
    {
        return $this->getLabelFromScore($this->getScore($text));
    }

    /**
     * Get label from score
     */
    private function getLabelFromScore(float $score): string
    {
        if ($score >= Config::POSITIVE_THRESHOLD) {
            return Config::LABEL_POSITIVE;
        }

        if ($score <= Config::NEGATIVE_THRESHOLD) {
            return Config::LABEL_NEGATIVE;
        }

        return Config::LABEL_NEUTRAL;
    }

    /**
     * @inheritDoc
     */
    public function isPositive(string $text): bool
    {
        return $this->getLabel($text) === Config::LABEL_POSITIVE;
    }

    /**
     * @inheritDoc
     */
    public function isNegative(string $text): bool
    {
        return $this->getLabel($text) === Config::LABEL_NEGATIVE;
    }

    /**
     * @inheritDoc
     */
    public function isNeutral(string $text): bool
    {
        return $this->getLabel($text) === Config::LABEL_NEUTRAL;
    }

    /**
     * @inheritDoc
     */
    public function getBreakdown(string $text): array
    {
        $words = $this->tokenize($text);
        $positiveFound = [];
        $negativeFound = [];

        foreach ($words as $word) {
            $normalized = $this->normalizeWord($word);

            if (isset($this->positiveWords[$normalized])) {
                $positiveFound[] = $word;
            } elseif (isset($this->negativeWords[$normalized])) {
                $negativeFound[] = $word;
            }
        }

        return [
            'positive_words' => $positiveFound,
            'negative_words' => $negativeFound,
            'score' => $this->getScore($text),
        ];
    }

    /**
     * @inheritDoc
     */
    public function addPositiveWords(array $words): void
    {
        foreach ($words as $word => $weight) {
            if (\is_int($word)) {
                // Array without weights
                $normalized = $this->normalizeWord((string) $weight);
                $this->positiveWords[$normalized] = 0.5;
            } else {
                // Array with weights
                $normalized = $this->normalizeWord($word);
                $this->positiveWords[$normalized] = (float) $weight;
            }
        }
    }

    /**
     * @inheritDoc
     */
    public function addNegativeWords(array $words): void
    {
        foreach ($words as $word => $weight) {
            if (\is_int($word)) {
                // Array without weights
                $normalized = $this->normalizeWord((string) $weight);
                $this->negativeWords[$normalized] = -0.5;
            } else {
                // Array with weights
                $normalized = $this->normalizeWord($word);
                $this->negativeWords[$normalized] = (float) $weight;
            }
        }
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
     * Calculate confidence score
     */
    private function calculateConfidence(string $text, float $score): float
    {
        $breakdown = $this->getBreakdown($text);
        $sentimentWordCount = \count($breakdown['positive_words']) + \count($breakdown['negative_words']);
        $totalWords = \count($this->tokenize($text));

        if ($totalWords === 0) {
            return 0.0;
        }

        // Confidence based on sentiment word coverage and score magnitude
        $coverage = $sentimentWordCount / $totalWords;
        $magnitude = \abs($score);

        return \min(1.0, ($coverage * 0.5 + $magnitude * 0.5));
    }

    /**
     * Analyze sentiment by sentences
     *
     * @return array<array{sentence: string, score: float, label: string}>
     */
    public function analyzeBySentence(string $text): array
    {
        $sentences = $this->splitSentences($text);
        $results = [];

        foreach ($sentences as $sentence) {
            $sentence = \trim($sentence);
            if (!empty($sentence)) {
                $score = $this->getScore($sentence);
                $results[] = [
                    'sentence' => $sentence,
                    'score' => $score,
                    'label' => $this->getLabelFromScore($score),
                ];
            }
        }

        return $results;
    }

    /**
     * Split text into sentences
     *
     * @return array<string>
     */
    private function splitSentences(string $text): array
    {
        $sentences = \preg_split('/[.!?؟]+/u', $text, -1, \PREG_SPLIT_NO_EMPTY);

        return $sentences !== false ? $sentences : [$text];
    }

    /**
     * Compare sentiment of two texts
     *
     * @return array{text1: array{score: float, label: string}, text2: array{score: float, label: string}, comparison: string}
     */
    public function compare(string $text1, string $text2): array
    {
        $score1 = $this->getScore($text1);
        $score2 = $this->getScore($text2);

        $comparison = 'equal';
        if ($score1 > $score2 + 0.1) {
            $comparison = 'text1_more_positive';
        } elseif ($score2 > $score1 + 0.1) {
            $comparison = 'text2_more_positive';
        }

        return [
            'text1' => [
                'score' => $score1,
                'label' => $this->getLabelFromScore($score1),
            ],
            'text2' => [
                'score' => $score2,
                'label' => $this->getLabelFromScore($score2),
            ],
            'comparison' => $comparison,
        ];
    }

    /**
     * Get overall sentiment statistics
     *
     * @return array{score: float, label: string, positive_count: int, negative_count: int, neutral_ratio: float}
     */
    public function getStatistics(string $text): array
    {
        $breakdown = $this->getBreakdown($text);
        $totalWords = \count($this->tokenize($text));
        $sentimentWords = \count($breakdown['positive_words']) + \count($breakdown['negative_words']);

        return [
            'score' => $breakdown['score'],
            'label' => $this->getLabelFromScore($breakdown['score']),
            'positive_count' => \count($breakdown['positive_words']),
            'negative_count' => \count($breakdown['negative_words']),
            'neutral_ratio' => $totalWords > 0 ? ($totalWords - $sentimentWords) / $totalWords : 1.0,
        ];
    }

    /**
     * Get positive word count
     */
    public function getPositiveWordCount(): int
    {
        return \count($this->positiveWords);
    }

    /**
     * Get negative word count
     */
    public function getNegativeWordCount(): int
    {
        return \count($this->negativeWords);
    }
}
