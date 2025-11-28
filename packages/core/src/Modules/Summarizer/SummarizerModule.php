<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Summarizer;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Modules\Summarizer\Contracts\SummarizerInterface;
use ArPHP\Core\Modules\Summarizer\Services\SummarizerService;

/**
 * Summarizer Module - PHP 8.4
 *
 * Arabic text summarization module.
 *
 * @package ArPHP\Core\Modules\Summarizer
 */
final class SummarizerModule extends AbstractModule implements SummarizerInterface
{
    private SummarizerService $service;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->service = new SummarizerService();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Summarizer';
    }

    /**
     * {@inheritdoc}
     */
    public function getVersion(): string
    {
        return '1.0.0';
    }

    /**
     * {@inheritdoc}
     */
    public function summarize(string $text, int $numSentences = 3): string
    {
        return $this->service->summarize($text, $numSentences);
    }

    /**
     * {@inheritdoc}
     */
    public function summarizeByRatio(string $text, float $ratio = 0.3): string
    {
        return $this->service->summarizeByRatio($text, $ratio);
    }

    /**
     * {@inheritdoc}
     */
    public function extractKeySentences(string $text, int $count = 5): array
    {
        return $this->service->extractKeySentences($text, $count);
    }

    /**
     * {@inheritdoc}
     */
    public function scoreSentences(string $text): array
    {
        return $this->service->scoreSentences($text);
    }

    /**
     * {@inheritdoc}
     */
    public function extractKeywords(string $text, int $count = 10): array
    {
        return $this->service->extractKeywords($text, $count);
    }

    /**
     * Split text into sentences
     *
     * @return array<string>
     */
    public function splitSentences(string $text): array
    {
        return $this->service->splitSentences($text);
    }

    /**
     * Get text statistics
     *
     * @return array<string, mixed>
     */
    public function getStatistics(string $text): array
    {
        return $this->service->getTextStatistics($text);
    }

    /**
     * Generate headline from text
     */
    public function generateHeadline(string $text, int $maxLength = 100): string
    {
        return $this->service->generateHeadline($text, $maxLength);
    }

    /**
     * Get service instance
     */
    public function getService(): SummarizerService
    {
        return $this->service;
    }
}
