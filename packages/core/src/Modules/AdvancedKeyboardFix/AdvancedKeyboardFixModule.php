<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\AdvancedKeyboardFix;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Modules\AdvancedKeyboardFix\Contracts\AdvancedKeyboardFixInterface;
use ArPHP\Core\Modules\AdvancedKeyboardFix\Services\AdvancedKeyboardFixService;

/**
 * Advanced Keyboard Fix Module - PHP 8.4
 *
 * Fixes Arabic text typed on wrong keyboard layout.
 *
 * @package ArPHP\Core\Modules\AdvancedKeyboardFix
 */
final class AdvancedKeyboardFixModule extends AbstractModule
{
    protected string $version = '1.0.0';

    /** @var array<string> */
    protected array $dependencies = [];

    private ?AdvancedKeyboardFixService $service = null;

    public function __construct()
    {
    }

    public function getName(): string
    {
        return 'advanced_keyboard_fix';
    }

    public function register(): void
    {
        $this->service = new AdvancedKeyboardFixService();
    }

    public function boot(): void
    {
        // Module ready
    }

    public function getService(): AdvancedKeyboardFixInterface
    {
        if ($this->service === null) {
            $this->register();
        }

        return $this->service;
    }

    /**
     * Fix Arabic text typed on English keyboard
     */
    public function fixArabicOnEnglish(string $text): string
    {
        return $this->getService()->fixArabicOnEnglish($text);
    }

    /**
     * Fix English text typed on Arabic keyboard
     */
    public function fixEnglishOnArabic(string $text): string
    {
        return $this->getService()->fixEnglishOnArabic($text);
    }

    /**
     * Auto-detect and fix layout issues
     */
    public function autoFix(string $text): string
    {
        return $this->getService()->autoFix($text);
    }

    /**
     * Detect keyboard layout
     *
     * @return 'arabic'|'english'|'mixed'|'unknown'
     */
    public function detectLayout(string $text): string
    {
        return $this->getService()->detectLayout($text);
    }

    /**
     * Check if text has layout issues
     */
    public function hasLayoutIssue(string $text): bool
    {
        return $this->getService()->hasLayoutIssue($text);
    }

    /**
     * Fix Franco-Arabic to Arabic
     */
    public function fixFrancoArabic(string $text): string
    {
        return $this->getService()->fixFrancoArabic($text);
    }

    /**
     * Fix common typing mistakes
     */
    public function fixTypingMistakes(string $text): string
    {
        return $this->getService()->fixTypingMistakes($text);
    }

    /**
     * Get keyboard map
     *
     * @return array<string, string>
     */
    public function getKeyboardMap(string $layout): array
    {
        return $this->getService()->getKeyboardMap($layout);
    }

    public static function getIdentifier(): string
    {
        return 'advanced_keyboard_fix';
    }

    /**
     * @return array<string>
     */
    public static function getPublicApi(): array
    {
        return [
            'fixArabicOnEnglish',
            'fixEnglishOnArabic',
            'autoFix',
            'detectLayout',
            'hasLayoutIssue',
            'fixFrancoArabic',
            'fixTypingMistakes',
        ];
    }
}
