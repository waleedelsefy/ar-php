<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\DialectNormalizer;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Modules\DialectNormalizer\Contracts\DialectNormalizerInterface;
use ArPHP\Core\Modules\DialectNormalizer\Services\DialectNormalizerService;

/**
 * DialectNormalizer Module - PHP 8.4
 *
 * @package ArPHP\Core\Modules\DialectNormalizer
 */
final class DialectNormalizerModule extends AbstractModule
{
    protected string $version = '1.0.0';

    /** @var array<string> */
    protected array $dependencies = [];

    private ?DialectNormalizerService $service = null;

    public function __construct()
    {
    }

    public function getName(): string
    {
        return 'dialect-normalizer';
    }

    public function register(): void
    {
        $this->service = new DialectNormalizerService();
    }

    public function boot(): void
    {
        // Module ready
    }

    public function getService(): DialectNormalizerInterface
    {
        if ($this->service === null) {
            $this->register();
        }

        return $this->service;
    }

    /**
     * Normalize dialect to MSA
     */
    public function normalize(string $text): string
    {
        return $this->getService()->normalize($text);
    }

    /**
     * Normalize specific dialect
     */
    public function normalizeDialect(string $text, string $dialect): string
    {
        return $this->getService()->normalizeDialect($text, $dialect);
    }

    /**
     * Detect dialect
     */
    public function detectDialect(string $text): string
    {
        return $this->getService()->detectDialect($text);
    }

    /**
     * Get dialect scores
     *
     * @return array<string, float>
     */
    public function getDialectScores(string $text): array
    {
        return $this->getService()->getDialectScores($text);
    }

    /**
     * Convert between dialects
     */
    public function convert(string $text, string $fromDialect, string $toDialect): string
    {
        return $this->getService()->convert($text, $fromDialect, $toDialect);
    }

    /**
     * Check if MSA
     */
    public function isMSA(string $text): bool
    {
        return $this->getService()->isMSA($text);
    }

    /**
     * Get supported dialects
     *
     * @return array<string>
     */
    public function getSupportedDialects(): array
    {
        return $this->getService()->getSupportedDialects();
    }

    public static function getIdentifier(): string
    {
        return 'dialect-normalizer';
    }

    /**
     * @return array<string>
     */
    public static function getPublicApi(): array
    {
        return [
            'normalize',
            'normalizeDialect',
            'detectDialect',
            'getDialectScores',
            'convert',
            'isMSA',
            'getSupportedDialects',
        ];
    }
}
