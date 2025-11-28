<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Summarizer\Contracts;

/**
 * Summarizer Interface - PHP 8.4
 *
 * Arabic text summarization interface.
 *
 * @package ArPHP\Core\Modules\Summarizer
 */
interface SummarizerInterface
{
    /**
     * Summarize text by extracting key sentences
     *
     * @param int $numSentences Number of sentences to extract
     */
    public function summarize(string $text, int $numSentences = 3): string;

    /**
     * Summarize by percentage of original text
     *
     * @param float $ratio Ratio of text to keep (0.0 to 1.0)
     */
    public function summarizeByRatio(string $text, float $ratio = 0.3): string;

    /**
     * Extract key sentences with scores
     *
     * @return array<int, array{sentence: string, score: float, position: int}>
     */
    public function extractKeySentences(string $text, int $count = 5): array;

    /**
     * Get sentence importance scores
     *
     * @return array<int, array{sentence: string, score: float}>
     */
    public function scoreSentences(string $text): array;

    /**
     * Extract keywords from text
     *
     * @return array<string, float>
     */
    public function extractKeywords(string $text, int $count = 10): array;
}
