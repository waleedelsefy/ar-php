<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Arabic;
use ArPHP\Core\Contracts\ServiceInterface;

/**
 * Sentiment Analysis Service - Dictionary-based
 * 
 * Analyzes sentiment of Arabic text using word dictionaries
 */
class SentimentService implements ServiceInterface
{
    /**
     * Positive words dictionary
     * 
     * @var array<string, float>
     */
    private const POSITIVE_WORDS = [
        'رائع' => 1.0,
        'ممتاز' => 1.0,
        'جميل' => 0.8,
        'جيد' => 0.7,
        'حلو' => 0.7,
        'مذهل' => 0.9,
        'عظيم' => 0.9,
        'ممتع' => 0.8,
        'سعيد' => 0.8,
        'فرح' => 0.8,
        'نجاح' => 0.8,
        'تميز' => 0.9,
        'إبداع' => 0.9,
        'احترافي' => 0.8,
        'مفيد' => 0.7,
        'أحب' => 0.8,
        'أنصح' => 0.7,
        'مميز' => 0.9,
        'ناجح' => 0.8,
        'متقن' => 0.8,
    ];

    /**
     * Negative words dictionary
     * 
     * @var array<string, float>
     */
    private const NEGATIVE_WORDS = [
        'سيئ' => -1.0,
        'فظيع' => -1.0,
        'رديء' => -0.9,
        'فاشل' => -0.9,
        'مريع' => -1.0,
        'كريه' => -0.8,
        'غبي' => -0.8,
        'حزين' => -0.7,
        'مشكلة' => -0.6,
        'خطأ' => -0.6,
        'أكره' => -0.9,
        'لا أنصح' => -0.8,
        'خيبة' => -0.8,
        'إحباط' => -0.7,
        'ضعيف' => -0.6,
        'مخيب' => -0.8,
        'بطيء' => -0.5,
        'معقد' => -0.5,
        'سيء' => -0.9,
        'قبيح' => -0.8,
    ];

    /**
     * Intensifiers
     * 
     * @var array<string, float>
     */
    private const INTENSIFIERS = [
        'جداً' => 1.5,
        'للغاية' => 1.5,
        'كثيراً' => 1.3,
        'حقاً' => 1.2,
        'فعلاً' => 1.2,
        'أبداً' => 1.4,
        'تماماً' => 1.3,
    ];

    /**
     * Negations
     * 
     * @var array<int, string>
     */
    private const NEGATIONS = [
        'ليس',
        'لا',
        'ما',
        'لم',
        'لن',
        'غير',
    ];

    /**
     * Analyze sentiment of text
     * 
     * @return array{sentiment: string, score: float, confidence: float, positive: int, negative: int, neutral: int}
     */
    public function analyze(string $text): array
    {
        $words = $this->tokenize($text);
        $scores = $this->calculateScores($words);
        
        $positive = $scores['positive'];
        $negative = $scores['negative'];
        $neutral = $scores['neutral'];
        
        $totalScore = $positive + $negative;
        $sentiment = 'neutral';
        $confidence = 0.0;
        
        if ($totalScore !== 0.0) {
            $normalizedScore = $totalScore / (abs($positive) + abs($negative));
            
            if ($normalizedScore > 0.2) {
                $sentiment = 'positive';
                $confidence = min(abs($normalizedScore), 1.0);
            } elseif ($normalizedScore < -0.2) {
                $sentiment = 'negative';
                $confidence = min(abs($normalizedScore), 1.0);
            } else {
                $sentiment = 'neutral';
                $confidence = 1.0 - abs($normalizedScore);
            }
        }
        
        return [
            'sentiment' => $sentiment,
            'score' => round($totalScore, 2),
            'confidence' => round($confidence, 2),
            'positive' => (int) round($positive * 10),
            'negative' => (int) round(abs($negative) * 10),
            'neutral' => $neutral,
        ];
    }

    /**
     * Analyze multiple texts
     * 
     * @param array<int, string> $texts
     * @return array<int, array{sentiment: string, score: float, confidence: float, positive: int, negative: int, neutral: int}>
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
     * Tokenize text into words
     * 
     * @return array<int, string>
     */
    private function tokenize(string $text): array
    {
        // Remove punctuation and split
        $text = preg_replace('/[^\p{Arabic}\s]/u', ' ', $text) ?? $text;
        $words = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        
        return $words !== false ? $words : [];
    }

    /**
     * Calculate sentiment scores
     * 
     * @param array<int, string> $words
     * @return array{positive: float, negative: float, neutral: int}
     */
    private function calculateScores(array $words): array
    {
        $positive = 0.0;
        $negative = 0.0;
        $neutral = 0;
        $negation = false;
        $intensifier = 1.0;
        
        foreach ($words as $word) {
            // Check for negation
            if (in_array($word, self::NEGATIONS, true)) {
                $negation = true;
                continue;
            }
            
            // Check for intensifier
            if (isset(self::INTENSIFIERS[$word])) {
                $intensifier = self::INTENSIFIERS[$word];
                continue;
            }
            
            // Check sentiment
            $score = 0.0;
            
            if (isset(self::POSITIVE_WORDS[$word])) {
                $score = self::POSITIVE_WORDS[$word];
            } elseif (isset(self::NEGATIVE_WORDS[$word])) {
                $score = self::NEGATIVE_WORDS[$word];
            } else {
                $neutral++;
                continue;
            }
            
            // Apply intensifier
            $score *= $intensifier;
            
            // Apply negation
            if ($negation) {
                $score *= -1;
            }
            
            // Add to totals
            if ($score > 0) {
                $positive += $score;
            } else {
                $negative += $score;
            }
            
            // Reset modifiers
            $negation = false;
            $intensifier = 1.0;
        }
        
        return [
            'positive' => $positive,
            'negative' => $negative,
            'neutral' => $neutral,
        ];
    }

    public function getServiceName(): string
    {
        return 'sentiment';
    }

    public function getConfig(): array
    {
        return [
            'version' => '1.0.0',
            'type' => 'dictionary-based',
            'features' => ['analyze', 'analyzeBatch', 'isPositive', 'isNegative'],
            'vocabulary_size' => count(self::POSITIVE_WORDS) + count(self::NEGATIVE_WORDS),
        ];
    }

    public function isAvailable(): bool
    {
        return extension_loaded('mbstring');
    }
}

/**
 * Sentiment Module
 * 
 * Registers the Sentiment service for Arabic sentiment analysis
 */
class SentimentModule extends AbstractModule
{
    protected string $version = '1.0.0';

    public function getName(): string
    {
        return 'sentiment';
    }

    public function register(): void
    {
        Arabic::container()->register('sentiment', function () {
            return new SentimentService();
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
