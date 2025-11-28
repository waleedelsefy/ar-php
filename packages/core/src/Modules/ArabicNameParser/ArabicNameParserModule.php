<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\ArabicNameParser;

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Modules\ArabicNameParser\Contracts\ArabicNameParserInterface;
use ArPHP\Core\Modules\ArabicNameParser\Services\ArabicNameParserService;

/**
 * Arabic Name Parser Module - PHP 8.4
 *
 * Parses and handles Arabic names with cultural awareness.
 *
 * @package ArPHP\Core\Modules\ArabicNameParser
 */
final class ArabicNameParserModule extends AbstractModule
{
    protected string $version = '1.0.0';

    /** @var array<string> */
    protected array $dependencies = [];

    private ?ArabicNameParserService $service = null;

    public function __construct()
    {
    }

    public function getName(): string
    {
        return 'arabic_name_parser';
    }

    public function register(): void
    {
        $this->service = new ArabicNameParserService();
    }

    public function boot(): void
    {
        // Module ready
    }

    public function getService(): ArabicNameParserInterface
    {
        if ($this->service === null) {
            $this->register();
        }

        return $this->service;
    }

    /**
     * Parse a full Arabic name
     *
     * @return array{
     *     prefix?: string,
     *     first_name: string,
     *     father_name?: string,
     *     grandfather_name?: string,
     *     family_name?: string,
     *     tribe?: string,
     *     suffix?: string,
     *     kunya?: string,
     *     laqab?: string,
     *     nisba?: string,
     *     full_name: string
     * }
     */
    public function parse(string $name): array
    {
        return $this->getService()->parse($name);
    }

    /**
     * Get first name
     */
    public function firstName(string $name): string
    {
        return $this->getService()->firstName($name);
    }

    /**
     * Get father's name
     */
    public function fatherName(string $name): ?string
    {
        return $this->getService()->fatherName($name);
    }

    /**
     * Get family name
     */
    public function familyName(string $name): ?string
    {
        return $this->getService()->familyName($name);
    }

    /**
     * Get kunya
     */
    public function kunya(string $name): ?string
    {
        return $this->getService()->kunya($name);
    }

    /**
     * Detect gender
     *
     * @return 'male'|'female'|'unknown'
     */
    public function detectGender(string $name): string
    {
        return $this->getService()->detectGender($name);
    }

    /**
     * Format name
     *
     * @param array<string, string> $parts
     */
    public function format(array $parts, string $style = 'full'): string
    {
        return $this->getService()->format($parts, $style);
    }

    /**
     * Normalize name
     */
    public function normalize(string $name): string
    {
        return $this->getService()->normalize($name);
    }

    /**
     * Check if name matches pattern
     */
    public function matches(string $name, string $pattern): bool
    {
        return $this->getService()->matches($name, $pattern);
    }

    /**
     * Split compound names
     *
     * @return array<string>
     */
    public function splitCompound(string $name): array
    {
        return $this->getService()->splitCompound($name);
    }

    public static function getIdentifier(): string
    {
        return 'arabic_name_parser';
    }

    /**
     * @return array<string>
     */
    public static function getPublicApi(): array
    {
        return [
            'parse',
            'firstName',
            'fatherName',
            'familyName',
            'kunya',
            'detectGender',
            'format',
            'normalize',
            'matches',
            'splitCompound',
        ];
    }
}
