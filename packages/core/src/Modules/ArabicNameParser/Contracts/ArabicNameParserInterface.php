<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\ArabicNameParser\Contracts;

/**
 * Arabic Name Parser Interface - PHP 8.4
 *
 * @package ArPHP\Core\Modules\ArabicNameParser
 */
interface ArabicNameParserInterface
{
    /**
     * Parse a full Arabic name into components
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
    public function parse(string $name): array;

    /**
     * Extract the first name (الاسم الأول)
     */
    public function firstName(string $name): string;

    /**
     * Extract the father's name (اسم الأب)
     */
    public function fatherName(string $name): ?string;

    /**
     * Extract the family/last name (اسم العائلة)
     */
    public function familyName(string $name): ?string;

    /**
     * Extract the kunya (الكنية) - e.g., أبو محمد
     */
    public function kunya(string $name): ?string;

    /**
     * Extract the laqab (اللقب) - title or nickname
     */
    public function laqab(string $name): ?string;

    /**
     * Extract the nisba (النسبة) - origin attribution
     */
    public function nisba(string $name): ?string;

    /**
     * Format name according to style
     *
     * @param array{
     *     prefix?: string,
     *     first_name: string,
     *     father_name?: string,
     *     grandfather_name?: string,
     *     family_name?: string,
     *     suffix?: string
     * } $parts
     */
    public function format(array $parts, string $style = 'full'): string;

    /**
     * Detect gender from name
     *
     * @return 'male'|'female'|'unknown'
     */
    public function detectGender(string $name): string;

    /**
     * Check if name matches a pattern
     */
    public function matches(string $name, string $pattern): bool;

    /**
     * Normalize Arabic name (remove diacritics, standardize)
     */
    public function normalize(string $name): string;

    /**
     * Split compound names
     *
     * @return array<string>
     */
    public function splitCompound(string $name): array;
}
