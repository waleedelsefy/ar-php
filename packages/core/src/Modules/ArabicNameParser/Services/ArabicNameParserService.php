<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\ArabicNameParser\Services;

use ArPHP\Core\Modules\ArabicNameParser\Config;
use ArPHP\Core\Modules\ArabicNameParser\Contracts\ArabicNameParserInterface;
use ArPHP\Core\Modules\ArabicNameParser\Exceptions\ArabicNameParserException;
use ArPHP\Core\Modules\ArabicNameParser\Helpers\GenderDetectorHelper;
use ArPHP\Core\Modules\ArabicNameParser\Helpers\NameNormalizerHelper;

/**
 * Arabic Name Parser Service - PHP 8.4
 *
 * @package ArPHP\Core\Modules\ArabicNameParser
 */
final class ArabicNameParserService implements ArabicNameParserInterface
{
    private readonly NameNormalizerHelper $normalizer;
    private readonly GenderDetectorHelper $genderDetector;

    public function __construct()
    {
        $this->normalizer = new NameNormalizerHelper();
        $this->genderDetector = new GenderDetectorHelper();
    }

    /**
     * @inheritDoc
     */
    public function parse(string $name): array
    {
        $name = $this->normalizer->cleanName($name);

        if ($name === '') {
            throw ArabicNameParserException::emptyName();
        }

        $result = [
            'full_name' => $name,
            'first_name' => '',
        ];

        $parts = $this->normalizer->splitName($name);

        if (empty($parts)) {
            throw ArabicNameParserException::invalidName($name);
        }

        // Extract prefix
        $prefixResult = $this->extractPrefix($parts);
        if ($prefixResult['prefix'] !== null) {
            $result['prefix'] = $prefixResult['prefix'];
            $parts = $prefixResult['remaining'];
        }

        // Extract suffix
        $suffixResult = $this->extractSuffix($parts);
        if ($suffixResult['suffix'] !== null) {
            $result['suffix'] = $suffixResult['suffix'];
            $parts = $suffixResult['remaining'];
        }

        // Extract kunya (أبو، أم، etc.)
        $kunyaResult = $this->extractKunya($parts);
        if ($kunyaResult['kunya'] !== null) {
            $result['kunya'] = $kunyaResult['kunya'];
            $parts = $kunyaResult['remaining'];
        }

        // Extract nisba
        $nisba = $this->extractNisba($parts);
        if ($nisba !== null) {
            $result['nisba'] = $nisba;
        }

        // Parse remaining parts
        $partsCount = \count($parts);

        if ($partsCount >= 1) {
            $result['first_name'] = $parts[0];
        }

        if ($partsCount >= 2) {
            // Check if second part is "بن" or "ابن"
            if (\in_array($parts[1], ['بن', 'ابن'], true) && $partsCount >= 3) {
                $result['father_name'] = $parts[2];

                if ($partsCount >= 4) {
                    $result['family_name'] = \implode(' ', \array_slice($parts, 3));
                }
            } else {
                $result['father_name'] = $parts[1];
            }
        }

        if ($partsCount >= 3 && !isset($result['family_name'])) {
            $result['grandfather_name'] = $parts[2];
        }

        if ($partsCount >= 4 && !isset($result['family_name'])) {
            $result['family_name'] = \implode(' ', \array_slice($parts, 3));
        }

        // Detect family name prefix (آل)
        if (isset($result['family_name'])) {
            foreach (Config::FAMILY_PREFIXES as $prefix) {
                if (\str_starts_with($result['family_name'], $prefix . ' ')) {
                    $result['tribe'] = $result['family_name'];
                    break;
                }
            }
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function firstName(string $name): string
    {
        $parsed = $this->parse($name);

        return $parsed['first_name'];
    }

    /**
     * @inheritDoc
     */
    public function fatherName(string $name): ?string
    {
        $parsed = $this->parse($name);

        return $parsed['father_name'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function familyName(string $name): ?string
    {
        $parsed = $this->parse($name);

        return $parsed['family_name'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function kunya(string $name): ?string
    {
        $parsed = $this->parse($name);

        return $parsed['kunya'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function laqab(string $name): ?string
    {
        $parsed = $this->parse($name);

        return $parsed['laqab'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function nisba(string $name): ?string
    {
        $parsed = $this->parse($name);

        return $parsed['nisba'] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function format(array $parts, string $style = 'full'): string
    {
        return match ($style) {
            Config::FORMAT_FULL => $this->formatFull($parts),
            Config::FORMAT_FORMAL => $this->formatFormal($parts),
            Config::FORMAT_SHORT => $this->formatShort($parts),
            Config::FORMAT_INITIALS => $this->formatInitials($parts),
            Config::FORMAT_WESTERN => $this->formatWestern($parts),
            default => throw ArabicNameParserException::invalidFormatStyle($style),
        };
    }

    /**
     * @inheritDoc
     */
    public function detectGender(string $name): string
    {
        return $this->genderDetector->detect($name);
    }

    /**
     * @inheritDoc
     */
    public function matches(string $name, string $pattern): bool
    {
        $normalizedName = $this->normalizer->normalizeForComparison($name);
        $normalizedPattern = $this->normalizer->normalizeForComparison($pattern);

        // Support wildcards
        $regex = \str_replace(
            ['*', '?'],
            ['.*', '.'],
            \preg_quote($normalizedPattern, '/')
        );

        return (bool) \preg_match('/^' . $regex . '$/u', $normalizedName);
    }

    /**
     * @inheritDoc
     */
    public function normalize(string $name): string
    {
        return $this->normalizer->normalize($name);
    }

    /**
     * @inheritDoc
     */
    public function splitCompound(string $name): array
    {
        $name = $this->normalizer->cleanName($name);

        // Check for "و" as separator
        if (\str_contains($name, ' و ')) {
            return \array_map(
                fn(string $part) => \trim($part),
                \explode(' و ', $name)
            );
        }

        // Check for comma separator
        if (\str_contains($name, '،') || \str_contains($name, ',')) {
            $parts = \preg_split('/[،,]/u', $name);

            return \array_map(
                fn(string $part) => \trim($part),
                $parts ?: []
            );
        }

        return [$name];
    }

    /**
     * Extract prefix from name parts
     *
     * @param array<string> $parts
     * @return array{prefix: ?string, remaining: array<string>}
     */
    private function extractPrefix(array $parts): array
    {
        if (empty($parts)) {
            return ['prefix' => null, 'remaining' => $parts];
        }

        $potentialPrefix = $parts[0];

        // Check for multi-word prefixes
        if (\count($parts) >= 2) {
            $twoWordPrefix = $parts[0] . ' ' . $parts[1];

            foreach (Config::PREFIXES as $prefix) {
                if ($twoWordPrefix === $prefix) {
                    return [
                        'prefix' => $twoWordPrefix,
                        'remaining' => \array_slice($parts, 2),
                    ];
                }
            }
        }

        // Check single word prefix
        foreach (Config::PREFIXES as $prefix) {
            if ($potentialPrefix === $prefix) {
                return [
                    'prefix' => $prefix,
                    'remaining' => \array_slice($parts, 1),
                ];
            }
        }

        return ['prefix' => null, 'remaining' => $parts];
    }

    /**
     * Extract suffix from name parts
     *
     * @param array<string> $parts
     * @return array{suffix: ?string, remaining: array<string>}
     */
    private function extractSuffix(array $parts): array
    {
        if (empty($parts)) {
            return ['suffix' => null, 'remaining' => $parts];
        }

        $lastPart = $parts[\count($parts) - 1];

        foreach (Config::SUFFIXES as $suffix) {
            if ($lastPart === $suffix) {
                return [
                    'suffix' => $suffix,
                    'remaining' => \array_slice($parts, 0, -1),
                ];
            }
        }

        return ['suffix' => null, 'remaining' => $parts];
    }

    /**
     * Extract kunya from name parts
     *
     * @param array<string> $parts
     * @return array{kunya: ?string, remaining: array<string>}
     */
    private function extractKunya(array $parts): array
    {
        if (\count($parts) < 2) {
            return ['kunya' => null, 'remaining' => $parts];
        }

        $firstPart = $parts[0];

        foreach (Config::KUNYA_PREFIXES as $prefix) {
            if ($firstPart === $prefix) {
                $kunya = $parts[0] . ' ' . $parts[1];

                return [
                    'kunya' => $kunya,
                    'remaining' => \array_slice($parts, 2),
                ];
            }
        }

        return ['kunya' => null, 'remaining' => $parts];
    }

    /**
     * Extract nisba from name parts
     *
     * @param array<string> $parts
     */
    private function extractNisba(array $parts): ?string
    {
        if (empty($parts)) {
            return null;
        }

        $lastPart = $parts[\count($parts) - 1];

        foreach (Config::NISBA_SUFFIXES as $suffix) {
            if (\mb_substr($lastPart, -\mb_strlen($suffix)) === $suffix) {
                // Likely a nisba
                if (\mb_strlen($lastPart) > 3) {
                    return $lastPart;
                }
            }
        }

        return null;
    }

    /**
     * Format name in full style
     *
     * @param array<string, string> $parts
     */
    private function formatFull(array $parts): string
    {
        $components = [];

        if (isset($parts['prefix'])) {
            $components[] = $parts['prefix'];
        }

        $components[] = $parts['first_name'];

        if (isset($parts['father_name'])) {
            $components[] = $parts['father_name'];
        }

        if (isset($parts['grandfather_name'])) {
            $components[] = $parts['grandfather_name'];
        }

        if (isset($parts['family_name'])) {
            $components[] = $parts['family_name'];
        }

        if (isset($parts['suffix'])) {
            $components[] = $parts['suffix'];
        }

        return \implode(' ', $components);
    }

    /**
     * Format name in formal style
     *
     * @param array<string, string> $parts
     */
    private function formatFormal(array $parts): string
    {
        $components = [];

        if (isset($parts['prefix'])) {
            $components[] = $parts['prefix'];
        }

        $components[] = $parts['first_name'];

        if (isset($parts['family_name'])) {
            $components[] = $parts['family_name'];
        }

        return \implode(' ', $components);
    }

    /**
     * Format name in short style
     *
     * @param array<string, string> $parts
     */
    private function formatShort(array $parts): string
    {
        return $parts['first_name'];
    }

    /**
     * Format name as initials
     *
     * @param array<string, string> $parts
     */
    private function formatInitials(array $parts): string
    {
        $initials = [];

        $initials[] = \mb_substr($parts['first_name'], 0, 1);

        if (isset($parts['father_name'])) {
            $initials[] = \mb_substr($parts['father_name'], 0, 1);
        }

        if (isset($parts['family_name'])) {
            $words = \explode(' ', $parts['family_name']);
            foreach ($words as $word) {
                if (!\in_array($word, Config::FAMILY_PREFIXES, true)) {
                    $initials[] = \mb_substr($word, 0, 1);
                    break;
                }
            }
        }

        return \implode('. ', $initials) . '.';
    }

    /**
     * Format name in Western style (Last, First)
     *
     * @param array<string, string> $parts
     */
    private function formatWestern(array $parts): string
    {
        if (isset($parts['family_name'])) {
            return $parts['family_name'] . '، ' . $parts['first_name'];
        }

        return $parts['first_name'];
    }
}
