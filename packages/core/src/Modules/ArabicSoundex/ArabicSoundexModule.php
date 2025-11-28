<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\ArabicSoundex;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Modules\ArabicSoundex\Contracts\ArabicSoundexInterface;
use ArPHP\Core\Modules\ArabicSoundex\Services\ArabicSoundexService;

/**
 * Arabic Soundex Module - PHP 8.4
 *
 * Provides phonetic algorithms for Arabic text matching and similarity.
 *
 * @package ArPHP\Core\Modules\ArabicSoundex
 */
final class ArabicSoundexModule extends AbstractModule
{
    protected string $version = '1.0.0';

    /** @var array<string> */
    protected array $dependencies = [];

    private ?ArabicSoundexService $service = null;

    /**
     * @param array{
     *     code_length?: int,
     *     use_extended?: bool
     * } $config
     */
    public function __construct(
        private array $config = []
    ) {
        $this->config = [
            'code_length' => $config['code_length'] ?? Config::DEFAULT_CODE_LENGTH,
            'use_extended' => $config['use_extended'] ?? false,
        ];
    }

    public function getName(): string
    {
        return 'arabic_soundex';
    }

    public function register(): void
    {
        $this->service = new ArabicSoundexService(
            $this->config['code_length'],
            $this->config['use_extended']
        );
    }

    public function boot(): void
    {
        // Module ready
    }

    public function getService(): ArabicSoundexInterface
    {
        if ($this->service === null) {
            $this->register();
        }

        return $this->service;
    }

    /**
     * Generate Arabic Soundex code
     */
    public function soundex(string $word): string
    {
        return $this->getService()->soundex($word);
    }

    /**
     * Generate Arabic Metaphone code
     */
    public function metaphone(string $word): string
    {
        return $this->getService()->metaphone($word);
    }

    /**
     * Check if two words sound similar
     */
    public function soundsLike(string $word1, string $word2): bool
    {
        return $this->getService()->soundsLike($word1, $word2);
    }

    /**
     * Get similarity score (0-100)
     */
    public function similarity(string $word1, string $word2): int
    {
        return $this->getService()->similarity($word1, $word2);
    }

    /**
     * Find similar words from a list
     *
     * @param array<string> $wordList
     * @return array<string, int>
     */
    public function findSimilar(string $word, array $wordList, int $threshold = 70): array
    {
        return $this->getService()->findSimilar($word, $wordList, $threshold);
    }

    /**
     * Get phonetic variants of a word
     *
     * @return array<string>
     */
    public function variants(string $word): array
    {
        return $this->getService()->getPhoneticVariants($word);
    }

    /**
     * Get phonetic key for indexing
     */
    public function phoneticKey(string $word): string
    {
        return $this->getService()->getPhoneticKey($word);
    }

    /**
     * Check if word matches pattern phonetically
     */
    public function matchesPattern(string $word, string $pattern): bool
    {
        return $this->getService()->matchesPattern($word, $pattern);
    }

    /**
     * Set code length
     */
    public function setCodeLength(int $length): self
    {
        $this->config['code_length'] = $length;

        if ($this->service !== null) {
            $this->service->setCodeLength($length);
        }

        return $this;
    }

    /**
     * Enable extended mode for finer phonetic distinction
     */
    public function setExtendedMode(bool $extended): self
    {
        $this->config['use_extended'] = $extended;

        if ($this->service !== null) {
            $this->service->setExtendedMode($extended);
        }

        return $this;
    }

    public static function getIdentifier(): string
    {
        return 'arabic_soundex';
    }

    /**
     * @return array<string>
     */
    public static function getPublicApi(): array
    {
        return [
            'soundex',
            'metaphone',
            'soundsLike',
            'similarity',
            'findSimilar',
            'variants',
            'phoneticKey',
            'matchesPattern',
        ];
    }
}
