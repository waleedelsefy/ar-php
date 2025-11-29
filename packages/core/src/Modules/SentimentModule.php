<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Arabic;

/**
 * Sentiment Analysis Service - Dictionary-based with JSON data files
 * 
 * Analyzes sentiment of Arabic text using comprehensive word dictionaries
 */
class SentimentService
{
    /**
     * Positive words dictionary loaded from JSON
     * @var array<string, float>
     */
    private array $positiveWords = [];

    /**
     * Negative words dictionary loaded from JSON
     * @var array<string, float>
     */
    private array $negativeWords = [];

    /**
     * Intensifiers loaded from JSON
     * @var array<string, float>
     */
    private array $intensifiers = [];

    /**
     * Negations loaded from JSON
     * @var array<int, string>
     */
    private array $negations = [];

    /**
     * Whether data has been loaded
     */
    private bool $loaded = false;

    /**
     * Load dictionaries from JSON files
     */
    private function loadDictionaries(): void
    {
        if ($this->loaded) {
            return;
        }

        $dataPath = __DIR__ . '/Sentiment/Data/';
        
        // Load positive words
        $positiveFile = $dataPath . 'positive_words.json';
        if (file_exists($positiveFile)) {
            $data = json_decode(file_get_contents($positiveFile), true);
            $this->positiveWords = $data['words'] ?? [];
        }
        
        // Load negative words
        $negativeFile = $dataPath . 'negative_words.json';
        if (file_exists($negativeFile)) {
            $data = json_decode(file_get_contents($negativeFile), true);
            $this->negativeWords = $data['words'] ?? [];
        }
        
        // Load modifiers
        $modifiersFile = $dataPath . 'modifiers.json';
        if (file_exists($modifiersFile)) {
            $data = json_decode(file_get_contents($modifiersFile), true);
            $this->intensifiers = $data['intensifiers'] ?? [];
            $this->negations = $data['negations'] ?? [];
        }
        
        // Fallback defaults if files not found
        if (empty($this->positiveWords)) {
            $this->positiveWords = $this->getDefaultPositiveWords();
        }
        if (empty($this->negativeWords)) {
            $this->negativeWords = $this->getDefaultNegativeWords();
        }
        if (empty($this->intensifiers)) {
            $this->intensifiers = $this->getDefaultIntensifiers();
        }
        if (empty($this->negations)) {
            $this->negations = $this->getDefaultNegations();
        }
        
        $this->loaded = true;
    }

    /**
     * Get default positive words
     * @return array<string, float>
     */
    private function getDefaultPositiveWords(): array
    {
        return [
            'رائع' => 1.0, 'ممتاز' => 1.0, 'جميل' => 0.8, 'جيد' => 0.7,
            'حلو' => 0.7, 'مذهل' => 0.9, 'عظيم' => 0.9, 'أحب' => 0.8,
        ];
    }

    /**
     * Get default negative words
     * @return array<string, float>
     */
    private function getDefaultNegativeWords(): array
    {
        return [
            'سيئ' => -1.0, 'سيء' => -1.0, 'فاشل' => -0.9, 'قبيح' => -0.8,
            'أكره' => -0.9, 'قذر' => -1.0, 'قذرة' => -1.0,
        ];
    }

    /**
     * Get default intensifiers
     * @return array<string, float>
     */
    private function getDefaultIntensifiers(): array
    {
        return [
            'جداً' => 1.5, 'جدا' => 1.5, 'للغاية' => 1.5, 'كثيراً' => 1.3,
        ];
    }

    /**
     * Get default negations
     * @return array<int, string>
     */
    private function getDefaultNegations(): array
    {
        return ['ليس', 'لا', 'ما', 'مش', 'لم', 'لن', 'غير'];
    }

    /**
     * Analyze sentiment of text
     * 
     * @return array{sentiment: string, score: float, confidence: float, positive: int, negative: int, neutral: int, positive_words: array, negative_words: array}
     */
    public function analyze(string $text): array
    {
        $this->loadDictionaries();
        
        $words = $this->tokenize($text);
        $scores = $this->calculateScores($words, $text);
        
        $positive = $scores['positive'];
        $negative = $scores['negative'];
        $neutral = $scores['neutral'];
        
        $totalScore = $positive + $negative;
        $sentiment = 'neutral';
        $confidence = 0.0;
        
        if (abs($positive) + abs($negative) > 0) {
            $normalizedScore = $totalScore / (abs($positive) + abs($negative));
            
            if ($normalizedScore > 0.1) {
                $sentiment = 'positive';
                $confidence = min(abs($normalizedScore), 1.0);
            } elseif ($normalizedScore < -0.1) {
                $sentiment = 'negative';
                $confidence = min(abs($normalizedScore), 1.0);
            } else {
                $sentiment = 'neutral';
                $confidence = 1.0 - abs($normalizedScore);
            }
        }
        
        return [
            'sentiment' => $sentiment,
            'label' => $sentiment,
            'score' => round($totalScore, 2),
            'confidence' => round($confidence, 2),
            'positive' => (int) round(abs($positive) * 10),
            'negative' => (int) round(abs($negative) * 10),
            'neutral' => $neutral,
            'positive_words' => $scores['found_positive'],
            'negative_words' => $scores['found_negative'],
        ];
    }

    /**
     * Analyze multiple texts
     * 
     * @param array<int, string> $texts
     * @return array<int, array>
     */
    public function analyzeBatch(array $texts): array
    {
        return array_map(fn($text) => $this->analyze($text), $texts);
    }

    /**
     * Check if text is positive
     */
    public function isPositive(string $text): bool
    {
        $result = $this->analyze($text);
        return $result['sentiment'] === 'positive';
    }

    /**
     * Check if text is negative
     */
    public function isNegative(string $text): bool
    {
        $result = $this->analyze($text);
        return $result['sentiment'] === 'negative';
    }

    /**
     * Check if text is neutral
     */
    public function isNeutral(string $text): bool
    {
        $result = $this->analyze($text);
        return $result['sentiment'] === 'neutral';
    }

    /**
     * Get sentiment score only
     */
    public function getScore(string $text): float
    {
        $result = $this->analyze($text);
        return $result['score'];
    }

    /**
     * Get sentiment label only
     */
    public function getLabel(string $text): string
    {
        $result = $this->analyze($text);
        return $result['label'];
    }

    /**
     * Get breakdown of sentiment analysis
     * @return array{positive_words: array, negative_words: array, score: float}
     */
    public function getBreakdown(string $text): array
    {
        $result = $this->analyze($text);
        return [
            'positive_words' => $result['positive_words'],
            'negative_words' => $result['negative_words'],
            'score' => $result['score'],
        ];
    }

    /**
     * Tokenize text into words
     * 
     * @return array<int, string>
     */
    private function tokenize(string $text): array
    {
        // Keep Arabic characters and spaces
        $text = preg_replace('/[^\p{Arabic}\s]/u', ' ', $text) ?? $text;
        $words = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        return $words !== false ? $words : [];
    }

    /**
     * Calculate sentiment scores
     * 
     * @param array<int, string> $words
     * @return array{positive: float, negative: float, neutral: int, found_positive: array, found_negative: array}
     */
    private function calculateScores(array $words, string $originalText): array
    {
        $positive = 0.0;
        $negative = 0.0;
        $neutral = 0;
        $foundPositive = [];
        $foundNegative = [];
        $negation = false;
        $intensifier = 1.0;
        
        // Also check for multi-word phrases in original text
        $this->checkPhrases($originalText, $positive, $negative, $foundPositive, $foundNegative);
        
        foreach ($words as $i => $word) {
            // Check for negation
            if (in_array($word, $this->negations, true)) {
                $negation = true;
                continue;
            }
            
            // Check for intensifier
            if (isset($this->intensifiers[$word])) {
                $intensifier = $this->intensifiers[$word];
                continue;
            }
            
            // Check sentiment
            $score = 0.0;
            $isPositive = false;
            $isNegative = false;
            
            if (isset($this->positiveWords[$word])) {
                $score = $this->positiveWords[$word];
                $isPositive = true;
            } elseif (isset($this->negativeWords[$word])) {
                $score = $this->negativeWords[$word];
                $isNegative = true;
            } else {
                $neutral++;
                // Reset negation after neutral word gap
                if ($negation && $i > 0) {
                    $negation = false;
                }
                continue;
            }
            
            // Apply intensifier
            $score *= $intensifier;
            
            // Apply negation (flip sentiment)
            if ($negation) {
                $score *= -1;
                $isPositive = !$isPositive;
                $isNegative = !$isNegative;
            }
            
            // Add to totals and track words
            if ($score > 0) {
                $positive += $score;
                if (!in_array($word, $foundPositive)) {
                    $foundPositive[] = $word;
                }
            } else {
                $negative += $score;
                if (!in_array($word, $foundNegative)) {
                    $foundNegative[] = $word;
                }
            }
            
            // Reset modifiers
            $negation = false;
            $intensifier = 1.0;
        }
        
        return [
            'positive' => $positive,
            'negative' => $negative,
            'neutral' => $neutral,
            'found_positive' => $foundPositive,
            'found_negative' => $foundNegative,
        ];
    }

    /**
     * Check for multi-word phrases
     */
    private function checkPhrases(string $text, float &$positive, float &$negative, array &$foundPositive, array &$foundNegative): void
    {
        // Check positive phrases
        foreach ($this->positiveWords as $phrase => $score) {
            if (mb_strlen($phrase) > 5 && mb_strpos($text, $phrase) !== false) {
                $positive += $score;
                $foundPositive[] = $phrase;
            }
        }
        
        // Check negative phrases  
        foreach ($this->negativeWords as $phrase => $score) {
            if (mb_strlen($phrase) > 5 && mb_strpos($text, $phrase) !== false) {
                $negative += $score;
                $foundNegative[] = $phrase;
            }
        }
    }
}

/**
 * Sentiment Analysis Module
 */
class SentimentModule extends AbstractModule
{
    protected string $version = '2.0.0';

    /** @var array<string> */
    protected array $dependencies = [];

    private ?SentimentService $service = null;

    public function getName(): string
    {
        return 'sentiment';
    }

    public function register(): void
    {
        $this->service = new SentimentService();
    }

    public function boot(): void
    {
        // Module ready
    }

    public function getService(): SentimentService
    {
        if ($this->service === null) {
            $this->register();
        }

        return $this->service;
    }

    /**
     * Analyze sentiment
     * @return array{sentiment: string, score: float, confidence: float, positive: int, negative: int, neutral: int, positive_words: array, negative_words: array}
     */
    public function analyze(string $text): array
    {
        return $this->getService()->analyze($text);
    }

    /**
     * Get sentiment score
     */
    public function getScore(string $text): float
    {
        return $this->getService()->getScore($text);
    }

    /**
     * Get sentiment label
     */
    public function getLabel(string $text): string
    {
        return $this->getService()->getLabel($text);
    }

    /**
     * Check if positive
     */
    public function isPositive(string $text): bool
    {
        return $this->getService()->isPositive($text);
    }

    /**
     * Check if negative
     */
    public function isNegative(string $text): bool
    {
        return $this->getService()->isNegative($text);
    }

    /**
     * Check if neutral
     */
    public function isNeutral(string $text): bool
    {
        return $this->getService()->isNeutral($text);
    }

    /**
     * Get breakdown
     * @return array{positive_words: array, negative_words: array, score: float}
     */
    public function getBreakdown(string $text): array
    {
        return $this->getService()->getBreakdown($text);
    }
}
