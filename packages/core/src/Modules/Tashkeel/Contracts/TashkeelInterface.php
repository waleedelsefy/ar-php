<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Tashkeel\Contracts;

/**
 * Tashkeel Interface - PHP 8.4
 *
 * Arabic diacritization (tashkeel/harakat).
 *
 * @package ArPHP\Core\Modules\Tashkeel
 */
interface TashkeelInterface
{
    /**
     * Remove all diacritics from text
     */
    public function removeTashkeel(string $text): string;

    /**
     * Remove specific diacritic
     */
    public function removeDiacritic(string $text, string $diacritic): string;

    /**
     * Check if text has diacritics
     */
    public function hasTashkeel(string $text): bool;

    /**
     * Get diacritics count
     */
    public function countTashkeel(string $text): int;

    /**
     * Get diacritic statistics
     *
     * @return array<string, int>
     */
    public function getDiacriticStats(string $text): array;

    /**
     * Extract only diacritics from text
     *
     * @return array<string>
     */
    public function extractTashkeel(string $text): array;

    /**
     * Add sukoon to unvocalized letters
     */
    public function addSukoon(string $text): string;

    /**
     * Normalize shadda combinations
     */
    public function normalizeShadda(string $text): string;
}
