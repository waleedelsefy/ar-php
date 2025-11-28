<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\AdvancedKeyboardFix\Contracts;

/**
 * Advanced Keyboard Fix Interface - PHP 8.4
 *
 * @package ArPHP\Core\Modules\AdvancedKeyboardFix
 */
interface AdvancedKeyboardFixInterface
{
    /**
     * Fix Arabic text typed on English keyboard layout
     */
    public function fixArabicOnEnglish(string $text): string;

    /**
     * Fix English text typed on Arabic keyboard layout
     */
    public function fixEnglishOnArabic(string $text): string;

    /**
     * Auto-detect and fix keyboard layout issues
     */
    public function autoFix(string $text): string;

    /**
     * Detect keyboard layout from text
     *
     * @return 'arabic'|'english'|'mixed'|'unknown'
     */
    public function detectLayout(string $text): string;

    /**
     * Check if text has keyboard layout issues
     */
    public function hasLayoutIssue(string $text): bool;

    /**
     * Fix Franco-Arabic (Arabizi) to Arabic
     */
    public function fixFrancoArabic(string $text): string;

    /**
     * Fix common Arabic typing mistakes
     */
    public function fixTypingMistakes(string $text): string;

    /**
     * Get the keyboard mapping for a layout
     *
     * @return array<string, string>
     */
    public function getKeyboardMap(string $layout): array;
}
