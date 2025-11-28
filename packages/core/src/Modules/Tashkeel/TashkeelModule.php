<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Tashkeel;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Modules\Tashkeel\Contracts\TashkeelInterface;
use ArPHP\Core\Modules\Tashkeel\Services\TashkeelService;

/**
 * Tashkeel Module - PHP 8.4
 *
 * Arabic diacritization module.
 *
 * @package ArPHP\Core\Modules\Tashkeel
 */
final class TashkeelModule extends AbstractModule implements TashkeelInterface
{
    private TashkeelService $service;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->service = new TashkeelService();
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Tashkeel';
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
    public function removeTashkeel(string $text): string
    {
        return $this->service->removeTashkeel($text);
    }

    /**
     * {@inheritdoc}
     */
    public function removeDiacritic(string $text, string $diacritic): string
    {
        return $this->service->removeDiacritic($text, $diacritic);
    }

    /**
     * {@inheritdoc}
     */
    public function hasTashkeel(string $text): bool
    {
        return $this->service->hasTashkeel($text);
    }

    /**
     * {@inheritdoc}
     */
    public function countTashkeel(string $text): int
    {
        return $this->service->countTashkeel($text);
    }

    /**
     * {@inheritdoc}
     */
    public function getDiacriticStats(string $text): array
    {
        return $this->service->getDiacriticStats($text);
    }

    /**
     * {@inheritdoc}
     */
    public function extractTashkeel(string $text): array
    {
        return $this->service->extractTashkeel($text);
    }

    /**
     * {@inheritdoc}
     */
    public function addSukoon(string $text): string
    {
        return $this->service->addSukoon($text);
    }

    /**
     * {@inheritdoc}
     */
    public function normalizeShadda(string $text): string
    {
        return $this->service->normalizeShadda($text);
    }

    /**
     * Remove only short vowels
     */
    public function removeShortVowels(string $text): string
    {
        return $this->service->removeShortVowels($text);
    }

    /**
     * Remove only tanween
     */
    public function removeTanween(string $text): string
    {
        return $this->service->removeTanween($text);
    }

    /**
     * Remove shadda only
     */
    public function removeShadda(string $text): string
    {
        return $this->service->removeShadda($text);
    }

    /**
     * Get tashkeel density
     */
    public function getTashkeelDensity(string $text): float
    {
        return $this->service->getTashkeelDensity($text);
    }

    /**
     * Check if letter is sun letter
     */
    public function isSunLetter(string $letter): bool
    {
        return $this->service->isSunLetter($letter);
    }

    /**
     * Check if letter is moon letter
     */
    public function isMoonLetter(string $letter): bool
    {
        return $this->service->isMoonLetter($letter);
    }

    /**
     * Get service instance
     */
    public function getService(): TashkeelService
    {
        return $this->service;
    }
}
