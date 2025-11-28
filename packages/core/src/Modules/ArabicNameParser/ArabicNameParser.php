<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\ArabicNameParser;

/**
 * ArabicNameParser Facade - PHP 8.4
 *
 * Static facade for easy access to Arabic Name Parser functionality.
 *
 * Usage:
 *   use ArPHP\Core\Modules\ArabicNameParser\ArabicNameParser;
 *
 *   $parsed = ArabicNameParser::parse('محمد بن عبدالله آل سعود');
 *   $firstName = ArabicNameParser::firstName('أحمد محمود السيد');
 *   $gender = ArabicNameParser::detectGender('فاطمة');
 *
 * @package ArPHP\Core\Modules\ArabicNameParser
 */
final class ArabicNameParser
{
    private static ?ArabicNameParserModule $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): ArabicNameParserModule
    {
        if (self::$instance === null) {
            self::$instance = new ArabicNameParserModule();
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
     * Parse a full Arabic name into components
     *
     * Example:
     *   ArabicNameParser::parse('الدكتور محمد بن عبدالله آل سعود');
     *   // [
     *   //     'prefix' => 'الدكتور',
     *   //     'first_name' => 'محمد',
     *   //     'father_name' => 'عبدالله',
     *   //     'family_name' => 'آل سعود',
     *   //     'tribe' => 'آل سعود',
     *   //     'full_name' => 'الدكتور محمد بن عبدالله آل سعود'
     *   // ]
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
    public static function parse(string $name): array
    {
        return self::getInstance()->parse($name);
    }

    /**
     * Extract the first name
     *
     * Example:
     *   ArabicNameParser::firstName('محمد أحمد السيد') // 'محمد'
     */
    public static function firstName(string $name): string
    {
        return self::getInstance()->firstName($name);
    }

    /**
     * Extract the father's name
     *
     * Example:
     *   ArabicNameParser::fatherName('محمد أحمد السيد') // 'أحمد'
     */
    public static function fatherName(string $name): ?string
    {
        return self::getInstance()->fatherName($name);
    }

    /**
     * Extract the family/last name
     *
     * Example:
     *   ArabicNameParser::familyName('محمد أحمد علي السيد') // 'السيد'
     */
    public static function familyName(string $name): ?string
    {
        return self::getInstance()->familyName($name);
    }

    /**
     * Extract the kunya (e.g., أبو محمد)
     *
     * Example:
     *   ArabicNameParser::kunya('أبو محمد أحمد السيد') // 'أبو محمد'
     */
    public static function kunya(string $name): ?string
    {
        return self::getInstance()->kunya($name);
    }

    /**
     * Detect gender from name
     *
     * Example:
     *   ArabicNameParser::detectGender('محمد') // 'male'
     *   ArabicNameParser::detectGender('فاطمة') // 'female'
     *   ArabicNameParser::detectGender('نور') // 'unknown'
     *
     * @return 'male'|'female'|'unknown'
     */
    public static function detectGender(string $name): string
    {
        return self::getInstance()->detectGender($name);
    }

    /**
     * Check if name is male
     */
    public static function isMale(string $name): bool
    {
        return self::detectGender($name) === 'male';
    }

    /**
     * Check if name is female
     */
    public static function isFemale(string $name): bool
    {
        return self::detectGender($name) === 'female';
    }

    /**
     * Format name parts according to style
     *
     * Styles:
     *   - 'full': Full name with all components
     *   - 'formal': Prefix + first name + family name
     *   - 'short': First name only
     *   - 'initials': م. أ. س.
     *   - 'western': Family name, First name
     *
     * @param array<string, string> $parts
     */
    public static function format(array $parts, string $style = 'full'): string
    {
        return self::getInstance()->format($parts, $style);
    }

    /**
     * Normalize Arabic name
     */
    public static function normalize(string $name): string
    {
        return self::getInstance()->normalize($name);
    }

    /**
     * Check if name matches a pattern (supports * and ? wildcards)
     *
     * Example:
     *   ArabicNameParser::matches('محمد أحمد', 'محمد *') // true
     */
    public static function matches(string $name, string $pattern): bool
    {
        return self::getInstance()->matches($name, $pattern);
    }

    /**
     * Split compound names
     *
     * Example:
     *   ArabicNameParser::splitCompound('محمد و أحمد')
     *   // ['محمد', 'أحمد']
     *
     * @return array<string>
     */
    public static function splitCompound(string $name): array
    {
        return self::getInstance()->splitCompound($name);
    }

    /**
     * Compare two names for similarity
     */
    public static function compare(string $name1, string $name2): bool
    {
        $normalized1 = self::normalize($name1);
        $normalized2 = self::normalize($name2);

        return $normalized1 === $normalized2;
    }

    /**
     * Get formal title for name based on gender
     */
    public static function getTitle(string $name): string
    {
        $gender = self::detectGender($name);

        return match ($gender) {
            'male' => 'السيد',
            'female' => 'السيدة',
            default => '',
        };
    }

    /**
     * Build full name from parts
     *
     * @param array<string, string> $parts
     */
    public static function build(array $parts): string
    {
        return self::format($parts, 'full');
    }

    /**
     * Extract all name components as array
     *
     * @return array<string>
     */
    public static function toArray(string $name): array
    {
        $parsed = self::parse($name);

        return \array_filter([
            $parsed['prefix'] ?? null,
            $parsed['first_name'],
            $parsed['father_name'] ?? null,
            $parsed['grandfather_name'] ?? null,
            $parsed['family_name'] ?? null,
            $parsed['suffix'] ?? null,
        ]);
    }
}
