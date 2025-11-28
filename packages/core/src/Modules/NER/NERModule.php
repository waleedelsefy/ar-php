<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\NER;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Modules\NER\Contracts\NERInterface;
use ArPHP\Core\Modules\NER\Services\NERService;

/**
 * NER Module - PHP 8.4
 *
 * Named Entity Recognition for Arabic text.
 *
 * @package ArPHP\Core\Modules\NER
 */
final class NERModule extends AbstractModule
{
    protected string $version = '1.0.0';

    /** @var array<string> */
    protected array $dependencies = [];

    private ?NERService $service = null;

    public function __construct()
    {
    }

    public function getName(): string
    {
        return 'ner';
    }

    public function register(): void
    {
        $this->service = new NERService();
    }

    public function boot(): void
    {
        // Module ready
    }

    public function getService(): NERInterface
    {
        if ($this->service === null) {
            $this->register();
        }

        return $this->service;
    }

    /**
     * Extract all entities
     *
     * @return array<array{entity: string, type: string, position: int}>
     */
    public function extract(string $text): array
    {
        return $this->getService()->extract($text);
    }

    /**
     * Extract by type
     *
     * @return array<string>
     */
    public function extractByType(string $text, string $type): array
    {
        return $this->getService()->extractByType($text, $type);
    }

    /**
     * Extract names
     *
     * @return array<string>
     */
    public function extractNames(string $text): array
    {
        return $this->getService()->extractNames($text);
    }

    /**
     * Extract locations
     *
     * @return array<string>
     */
    public function extractLocations(string $text): array
    {
        return $this->getService()->extractLocations($text);
    }

    /**
     * Extract organizations
     *
     * @return array<string>
     */
    public function extractOrganizations(string $text): array
    {
        return $this->getService()->extractOrganizations($text);
    }

    /**
     * Extract dates
     *
     * @return array<string>
     */
    public function extractDates(string $text): array
    {
        return $this->getService()->extractDates($text);
    }

    /**
     * Tag text with entities
     */
    public function tag(string $text): string
    {
        return $this->getService()->tag($text);
    }

    /**
     * Get entity types in text
     *
     * @return array<string>
     */
    public function getEntityTypes(string $text): array
    {
        return $this->getService()->getEntityTypes($text);
    }

    public static function getIdentifier(): string
    {
        return 'ner';
    }

    /**
     * @return array<string>
     */
    public static function getPublicApi(): array
    {
        return [
            'extract',
            'extractByType',
            'extractNames',
            'extractLocations',
            'extractOrganizations',
            'extractDates',
            'tag',
            'getEntityTypes',
        ];
    }
}
