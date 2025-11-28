<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\NER;

/**
 * NER Facade - PHP 8.4
 *
 * Static facade for Named Entity Recognition.
 *
 * Usage:
 *   use ArPHP\Core\Modules\NER\NER;
 *
 *   $entities = NER::extract('زار محمد مدينة الرياض');
 *   $names = NER::names($text);
 *   $locations = NER::locations($text);
 *
 * @package ArPHP\Core\Modules\NER
 */
final class NER
{
    private static ?NERModule $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): NERModule
    {
        if (self::$instance === null) {
            self::$instance = new NERModule();
            self::$instance->register();
        }

        return self::$instance;
    }

    /**
     * Reset the singleton instance
     */
    public static function reset(): void
    {
        self::$instance = null;
    }

    /**
     * Extract all entities
     *
     * @return array<array{entity: string, type: string, position: int}>
     */
    public static function extract(string $text): array
    {
        return self::getInstance()->extract($text);
    }

    /**
     * Extract entities by type
     *
     * @return array<string>
     */
    public static function extractByType(string $text, string $type): array
    {
        return self::getInstance()->extractByType($text, $type);
    }

    /**
     * Extract person names
     *
     * @return array<string>
     */
    public static function names(string $text): array
    {
        return self::getInstance()->extractNames($text);
    }

    /**
     * Alias for names
     *
     * @return array<string>
     */
    public static function extractNames(string $text): array
    {
        return self::names($text);
    }

    /**
     * Alias for names
     *
     * @return array<string>
     */
    public static function persons(string $text): array
    {
        return self::names($text);
    }

    /**
     * Extract locations
     *
     * @return array<string>
     */
    public static function locations(string $text): array
    {
        return self::getInstance()->extractLocations($text);
    }

    /**
     * Alias for locations
     *
     * @return array<string>
     */
    public static function extractLocations(string $text): array
    {
        return self::locations($text);
    }

    /**
     * Alias for locations
     *
     * @return array<string>
     */
    public static function places(string $text): array
    {
        return self::locations($text);
    }

    /**
     * Extract organizations
     *
     * @return array<string>
     */
    public static function organizations(string $text): array
    {
        return self::getInstance()->extractOrganizations($text);
    }

    /**
     * Alias for organizations
     *
     * @return array<string>
     */
    public static function extractOrganizations(string $text): array
    {
        return self::organizations($text);
    }

    /**
     * Alias for organizations
     *
     * @return array<string>
     */
    public static function orgs(string $text): array
    {
        return self::organizations($text);
    }

    /**
     * Extract dates
     *
     * @return array<string>
     */
    public static function dates(string $text): array
    {
        return self::getInstance()->extractDates($text);
    }

    /**
     * Alias for dates
     *
     * @return array<string>
     */
    public static function extractDates(string $text): array
    {
        return self::dates($text);
    }

    /**
     * Extract emails
     *
     * @return array<string>
     */
    public static function emails(string $text): array
    {
        return self::extractByType($text, Config::TYPE_EMAIL);
    }

    /**
     * Extract URLs
     *
     * @return array<string>
     */
    public static function urls(string $text): array
    {
        return self::extractByType($text, Config::TYPE_URL);
    }

    /**
     * Extract phone numbers
     *
     * @return array<string>
     */
    public static function phones(string $text): array
    {
        return self::extractByType($text, Config::TYPE_PHONE);
    }

    /**
     * Extract money/currency
     *
     * @return array<string>
     */
    public static function money(string $text): array
    {
        return self::extractByType($text, Config::TYPE_MONEY);
    }

    /**
     * Extract percentages
     *
     * @return array<string>
     */
    public static function percentages(string $text): array
    {
        return self::extractByType($text, Config::TYPE_PERCENTAGE);
    }

    /**
     * Tag text with entity annotations
     */
    public static function tag(string $text): string
    {
        return self::getInstance()->tag($text);
    }

    /**
     * Get entity types found in text
     *
     * @return array<string>
     */
    public static function types(string $text): array
    {
        return self::getInstance()->getEntityTypes($text);
    }

    /**
     * Alias for types
     *
     * @return array<string>
     */
    public static function getEntityTypes(string $text): array
    {
        return self::types($text);
    }

    /**
     * Count entities by type
     *
     * @return array<string, int>
     */
    public static function countByType(string $text): array
    {
        /** @var \ArPHP\Core\Modules\NER\Services\NERService $service */
        $service = self::getInstance()->getService();

        return $service->countByType($text);
    }

    /**
     * Add custom names
     *
     * @param array<string> $names
     */
    public static function addNames(array $names): void
    {
        /** @var \ArPHP\Core\Modules\NER\Services\NERService $service */
        $service = self::getInstance()->getService();

        $service->addNames($names);
    }

    /**
     * Add custom locations
     *
     * @param array<string> $locations
     */
    public static function addLocations(array $locations): void
    {
        /** @var \ArPHP\Core\Modules\NER\Services\NERService $service */
        $service = self::getInstance()->getService();

        $service->addLocations($locations);
    }

    /**
     * Add custom organizations
     *
     * @param array<string> $organizations
     */
    public static function addOrganizations(array $organizations): void
    {
        /** @var \ArPHP\Core\Modules\NER\Services\NERService $service */
        $service = self::getInstance()->getService();

        $service->addOrganizations($organizations);
    }

    /**
     * Check if text contains any entities
     */
    public static function hasEntities(string $text): bool
    {
        return \count(self::extract($text)) > 0;
    }

    /**
     * Get entity count
     */
    public static function count(string $text): int
    {
        return \count(self::extract($text));
    }
}
