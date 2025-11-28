<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Arabic;
use ArPHP\Core\Contracts\ServiceInterface;

/**
 * Text Statistics Service
 * 
 * Provides detailed statistics about Arabic text
 */
class StatisticsService implements ServiceInterface
{
    /**
     * Analyze text and return comprehensive statistics
     * 
     * @return array<string, mixed>
     */
    public function analyze(string $text): array
    {
        return [
            'characters' => $this->countCharacters($text),
            'words' => $this->countWords($text),
            'sentences' => $this->countSentences($text),
            'paragraphs' => $this->countParagraphs($text),
            'averages' => $this->calculateAverages($text),
            'unique_words' => $this->countUniqueWords($text),
            'word_frequency' => $this->getWordFrequency($text, 10),
            'readability' => $this->calculateReadability($text),
        ];
    }

    /**
     * Count characters (excluding spaces)
     */
    public function countCharacters(string $text): int
    {
        $text = preg_replace('/\s+/u', '', $text) ?? '';
        return mb_strlen($text, 'UTF-8');
    }

    /**
     * Count words
     */
    public function countWords(string $text): int
    {
        $words = preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY);
        return $words !== false ? count($words) : 0;
    }

    /**
     * Count sentences
     */
    public function countSentences(string $text): int
    {
        $count = preg_match_all('/[.!?؟]+/u', $text);
        return max(1, $count);
    }

    /**
     * Count paragraphs
     */
    public function countParagraphs(string $text): int
    {
        $paragraphs = preg_split('/\n\s*\n/', trim($text), -1, PREG_SPLIT_NO_EMPTY);
        return $paragraphs !== false ? count($paragraphs) : 1;
    }

    /**
     * Calculate averages
     * 
     * @return array{word_length: float, words_per_sentence: float, sentences_per_paragraph: float}
     */
    public function calculateAverages(string $text): array
    {
        $words = $this->getWords($text);
        $wordCount = count($words);
        $sentenceCount = $this->countSentences($text);
        $paragraphCount = $this->countParagraphs($text);
        
        // Average word length
        $totalLength = 0;
        foreach ($words as $word) {
            $totalLength += mb_strlen($word, 'UTF-8');
        }
        $avgWordLength = $wordCount > 0 ? $totalLength / $wordCount : 0;
        
        // Words per sentence
        $wordsPerSentence = $sentenceCount > 0 ? $wordCount / $sentenceCount : 0;
        
        // Sentences per paragraph
        $sentencesPerParagraph = $paragraphCount > 0 ? $sentenceCount / $paragraphCount : 0;
        
        return [
            'word_length' => round($avgWordLength, 2),
            'words_per_sentence' => round($wordsPerSentence, 1),
            'sentences_per_paragraph' => round($sentencesPerParagraph, 1),
        ];
    }

    /**
     * Count unique words
     */
    public function countUniqueWords(string $text): int
    {
        $words = $this->getWords($text);
        $unique = array_unique($words);
        return count($unique);
    }

    /**
     * Get word frequency
     * 
     * @return array<string, int>
     */
    public function getWordFrequency(string $text, int $limit = 10): array
    {
        $words = $this->getWords($text);
        $frequency = array_count_values($words);
        arsort($frequency);
        
        return array_slice($frequency, 0, $limit, true);
    }

    /**
     * Calculate readability score (simplified)
     * 
     * Based on average word length and sentence length
     * Score: 1 (very easy) to 10 (very hard)
     */
    public function calculateReadability(string $text): float
    {
        $averages = $this->calculateAverages($text);
        
        // Simplified readability formula
        $wordLengthScore = min($averages['word_length'] / 2, 5);
        $sentenceLengthScore = min($averages['words_per_sentence'] / 5, 5);
        
        $score = ($wordLengthScore + $sentenceLengthScore) / 2;
        
        return round($score, 1);
    }

    /**
     * Get lexical diversity (unique words / total words)
     */
    public function getLexicalDiversity(string $text): float
    {
        $totalWords = $this->countWords($text);
        $uniqueWords = $this->countUniqueWords($text);
        
        if ($totalWords === 0) {
            return 0.0;
        }
        
        return round($uniqueWords / $totalWords, 2);
    }

    /**
     * Get text summary
     * 
     * @return array{length: string, complexity: string, diversity: string}
     */
    public function getSummary(string $text): array
    {
        $wordCount = $this->countWords($text);
        $readability = $this->calculateReadability($text);
        $diversity = $this->getLexicalDiversity($text);
        
        // Length classification
        $length = match (true) {
            $wordCount < 50 => 'very_short',
            $wordCount < 150 => 'short',
            $wordCount < 300 => 'medium',
            $wordCount < 600 => 'long',
            default => 'very_long',
        };
        
        // Complexity classification
        $complexity = match (true) {
            $readability < 3 => 'very_easy',
            $readability < 5 => 'easy',
            $readability < 7 => 'medium',
            $readability < 9 => 'hard',
            default => 'very_hard',
        };
        
        // Diversity classification
        $diversityLevel = match (true) {
            $diversity < 0.3 => 'low',
            $diversity < 0.5 => 'medium',
            $diversity < 0.7 => 'high',
            default => 'very_high',
        };
        
        return [
            'length' => $length,
            'complexity' => $complexity,
            'diversity' => $diversityLevel,
        ];
    }

    /**
     * Get words from text
     * 
     * @return array<int, string>
     */
    private function getWords(string $text): array
    {
        // Remove punctuation
        $text = preg_replace('/[^\p{Arabic}\s]/u', ' ', $text) ?? $text;
        
        // Split into words
        $words = preg_split('/\s+/u', trim($text), -1, PREG_SPLIT_NO_EMPTY);
        
        return $words !== false ? $words : [];
    }

    public function getServiceName(): string
    {
        return 'statistics';
    }

    public function getConfig(): array
    {
        return [
            'version' => '1.0.0',
            'features' => [
                'analyze',
                'countCharacters',
                'countWords',
                'countSentences',
                'getWordFrequency',
                'calculateReadability',
                'getLexicalDiversity',
                'getSummary',
            ],
        ];
    }

    public function isAvailable(): bool
    {
        return extension_loaded('mbstring');
    }
}

/**
 * Statistics Module
 * 
 * Registers the Statistics service for text analysis
 */
class StatisticsModule extends AbstractModule
{
    protected string $version = '1.0.0';

    public function getName(): string
    {
        return 'statistics';
    }

    public function register(): void
    {
        Arabic::container()->register('statistics', function () {
            return new StatisticsService();
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
