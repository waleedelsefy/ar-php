<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\SpellChecker;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Modules\SpellChecker\Contracts\SpellCheckerInterface;
use ArPHP\Core\Modules\SpellChecker\Services\SpellCheckerService;

/**
 * SpellChecker Module - PHP 8.4
 *
 * @package ArPHP\Core\Modules\SpellChecker
 */
final class SpellCheckerModule extends AbstractModule
{
    protected string $version = '1.0.0';

    /** @var array<string> */
    protected array $dependencies = [];

    private ?SpellCheckerService $service = null;

    public function __construct()
    {
    }

    public function getName(): string
    {
        return 'spell-checker';
    }

    public function register(): void
    {
        $this->service = new SpellCheckerService();
    }

    public function boot(): void
    {
        // Module ready
    }

    public function getService(): SpellCheckerInterface
    {
        if ($this->service === null) {
            $this->register();
        }

        return $this->service;
    }

    /**
     * Check if word is spelled correctly
     */
    public function check(string $word): bool
    {
        return $this->getService()->check($word);
    }

    /**
     * Get suggestions for word
     *
     * @return array<string>
     */
    public function suggest(string $word, int $limit = 5): array
    {
        return $this->getService()->suggest($word, $limit);
    }

    /**
     * Check text for spelling errors
     *
     * @return array<array{word: string, position: int, suggestions: array<string>}>
     */
    public function checkText(string $text): array
    {
        return $this->getService()->checkText($text);
    }

    /**
     * Add word to dictionary
     */
    public function addWord(string $word): void
    {
        $this->getService()->addWord($word);
    }

    /**
     * Add multiple words
     *
     * @param array<string> $words
     */
    public function addWords(array $words): void
    {
        $this->getService()->addWords($words);
    }

    /**
     * Remove word from dictionary
     */
    public function removeWord(string $word): void
    {
        $this->getService()->removeWord($word);
    }

    /**
     * Check if word exists in dictionary
     */
    public function exists(string $word): bool
    {
        return $this->getService()->exists($word);
    }

    /**
     * Get dictionary size
     */
    public function getDictionarySize(): int
    {
        return $this->getService()->getDictionarySize();
    }

    /**
     * Calculate edit distance
     */
    public function editDistance(string $word1, string $word2): int
    {
        return $this->getService()->editDistance($word1, $word2);
    }

    public static function getIdentifier(): string
    {
        return 'spell-checker';
    }

    /**
     * @return array<string>
     */
    public static function getPublicApi(): array
    {
        return [
            'check',
            'suggest',
            'checkText',
            'addWord',
            'addWords',
            'removeWord',
            'exists',
            'getDictionarySize',
            'editDistance',
        ];
    }
}
