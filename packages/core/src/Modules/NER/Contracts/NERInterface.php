<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\NER\Contracts;

/**
 * NER Interface - PHP 8.4
 *
 * Named Entity Recognition for Arabic text.
 *
 * @package ArPHP\Core\Modules\NER
 */
interface NERInterface
{
    /**
     * Extract all named entities from text
     *
     * @return array<array{entity: string, type: string, position: int}>
     */
    public function extract(string $text): array;

    /**
     * Extract entities by type
     *
     * @return array<string>
     */
    public function extractByType(string $text, string $type): array;

    /**
     * Extract person names
     *
     * @return array<string>
     */
    public function extractNames(string $text): array;

    /**
     * Extract locations
     *
     * @return array<string>
     */
    public function extractLocations(string $text): array;

    /**
     * Extract organizations
     *
     * @return array<string>
     */
    public function extractOrganizations(string $text): array;

    /**
     * Extract dates
     *
     * @return array<string>
     */
    public function extractDates(string $text): array;

    /**
     * Tag text with entity annotations
     */
    public function tag(string $text): string;

    /**
     * Get entity types found in text
     *
     * @return array<string>
     */
    public function getEntityTypes(string $text): array;
}
