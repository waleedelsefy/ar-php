<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\DialectNormalizer\Contracts;

/**
 * DialectNormalizer Interface - PHP 8.4
 *
 * @package ArPHP\Core\Modules\DialectNormalizer
 */
interface DialectNormalizerInterface
{
    /**
     * Normalize dialect to Modern Standard Arabic
     */
    public function normalize(string $text): string;

    /**
     * Normalize specific dialect
     */
    public function normalizeDialect(string $text, string $dialect): string;

    /**
     * Detect dialect of text
     */
    public function detectDialect(string $text): string;

    /**
     * Get dialect confidence scores
     *
     * @return array<string, float>
     */
    public function getDialectScores(string $text): array;

    /**
     * Convert between dialects
     */
    public function convert(string $text, string $fromDialect, string $toDialect): string;

    /**
     * Check if text is MSA
     */
    public function isMSA(string $text): bool;

    /**
     * Get supported dialects
     *
     * @return array<string>
     */
    public function getSupportedDialects(): array;
}
