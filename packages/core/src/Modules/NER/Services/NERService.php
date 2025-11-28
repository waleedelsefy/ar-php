<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\NER\Services;

use ArPHP\Core\Modules\NER\Config;
use ArPHP\Core\Modules\NER\Contracts\NERInterface;
use ArPHP\Core\Modules\NER\Exceptions\NERException;

/**
 * NER Service - PHP 8.4
 *
 * Rule-based Named Entity Recognition for Arabic text.
 *
 * @package ArPHP\Core\Modules\NER
 */
final class NERService implements NERInterface
{
    /** @var array<string, bool> */
    private array $namesLookup = [];

    /** @var array<string, bool> */
    private array $locationsLookup = [];

    /** @var array<string, bool> */
    private array $organizationsLookup = [];

    public function __construct()
    {
        $this->buildLookupTables();
    }

    /**
     * Build lookup tables for fast matching
     */
    private function buildLookupTables(): void
    {
        foreach (Config::COMMON_NAMES as $name) {
            $this->namesLookup[$this->normalize($name)] = true;
        }

        foreach (Config::COMMON_LOCATIONS as $location) {
            $this->locationsLookup[$this->normalize($location)] = true;
        }

        foreach (Config::COMMON_ORGANIZATIONS as $org) {
            $this->organizationsLookup[$this->normalize($org)] = true;
        }
    }

    /**
     * Normalize text for comparison
     */
    private function normalize(string $text): string
    {
        // Remove diacritics
        $diacritics = ['ً', 'ٌ', 'ٍ', 'َ', 'ُ', 'ِ', 'ّ', 'ْ', 'ـ'];
        $text = \str_replace($diacritics, '', $text);

        // Normalize Alef
        $text = \str_replace(['أ', 'إ', 'آ', 'ٱ'], 'ا', $text);

        return \trim($text);
    }

    /**
     * @inheritDoc
     */
    public function extract(string $text): array
    {
        $entities = [];

        // Extract all entity types
        $entities = \array_merge($entities, $this->findPersons($text));
        $entities = \array_merge($entities, $this->findLocations($text));
        $entities = \array_merge($entities, $this->findOrganizations($text));
        $entities = \array_merge($entities, $this->findDates($text));
        $entities = \array_merge($entities, $this->findEmails($text));
        $entities = \array_merge($entities, $this->findUrls($text));
        $entities = \array_merge($entities, $this->findPhones($text));
        $entities = \array_merge($entities, $this->findMoney($text));
        $entities = \array_merge($entities, $this->findPercentages($text));

        // Sort by position
        \usort($entities, fn($a, $b) => $a['position'] <=> $b['position']);

        return $entities;
    }

    /**
     * @inheritDoc
     */
    public function extractByType(string $text, string $type): array
    {
        if (!\in_array($type, Config::VALID_TYPES, true)) {
            throw NERException::invalidEntityType($type);
        }

        $entities = $this->extract($text);

        $filtered = \array_filter($entities, fn($e) => $e['type'] === $type);

        return \array_column($filtered, 'entity');
    }

    /**
     * @inheritDoc
     */
    public function extractNames(string $text): array
    {
        return $this->extractByType($text, Config::TYPE_PERSON);
    }

    /**
     * @inheritDoc
     */
    public function extractLocations(string $text): array
    {
        return $this->extractByType($text, Config::TYPE_LOCATION);
    }

    /**
     * @inheritDoc
     */
    public function extractOrganizations(string $text): array
    {
        return $this->extractByType($text, Config::TYPE_ORGANIZATION);
    }

    /**
     * @inheritDoc
     */
    public function extractDates(string $text): array
    {
        return $this->extractByType($text, Config::TYPE_DATE);
    }

    /**
     * @inheritDoc
     */
    public function tag(string $text): string
    {
        $entities = $this->extract($text);

        // Sort by position descending to replace from end
        \usort($entities, fn($a, $b) => $b['position'] <=> $a['position']);

        $result = $text;
        foreach ($entities as $entity) {
            $tag = "[{$entity['type']}:{$entity['entity']}]";
            $result = \mb_substr($result, 0, $entity['position'])
                . $tag
                . \mb_substr($result, $entity['position'] + \mb_strlen($entity['entity']));
        }

        return $result;
    }

    /**
     * @inheritDoc
     */
    public function getEntityTypes(string $text): array
    {
        $entities = $this->extract($text);

        return \array_unique(\array_column($entities, 'type'));
    }

    /**
     * Find person names
     *
     * @return array<array{entity: string, type: string, position: int}>
     */
    private function findPersons(string $text): array
    {
        $entities = [];
        $words = $this->tokenize($text);

        for ($i = 0; $i < \count($words); $i++) {
            $word = $words[$i];
            $normalized = $this->normalize($word);

            // Check for name prefixes
            if (\in_array($word, Config::NAME_PREFIXES, true) && isset($words[$i + 1])) {
                // Name prefix followed by name
                $name = $word . ' ' . $words[$i + 1];
                if (isset($words[$i + 2]) && $this->isLikelyName($words[$i + 2])) {
                    $name .= ' ' . $words[$i + 2];
                }
                $position = \mb_strpos($text, $name);
                if ($position !== false) {
                    $entities[] = [
                        'entity' => $name,
                        'type' => Config::TYPE_PERSON,
                        'position' => $position,
                    ];
                }
            } elseif (isset($this->namesLookup[$normalized])) {
                // Known name
                $position = \mb_strpos($text, $word);
                if ($position !== false) {
                    // Try to get full name (first + last)
                    $fullName = $word;
                    if (isset($words[$i + 1]) && $this->isLikelyName($words[$i + 1])) {
                        $fullName .= ' ' . $words[$i + 1];
                    }
                    $entities[] = [
                        'entity' => $fullName,
                        'type' => Config::TYPE_PERSON,
                        'position' => $position,
                    ];
                }
            }
        }

        return $entities;
    }

    /**
     * Check if word is likely a name
     */
    private function isLikelyName(string $word): bool
    {
        $normalized = $this->normalize($word);

        // Check known names
        if (isset($this->namesLookup[$normalized])) {
            return true;
        }

        // Check if starts with capital-like patterns in Arabic context
        // Names often start with عبد, آل, بن
        $namePatterns = ['عبد', 'آل', 'بن', 'أبو', 'أم'];
        foreach ($namePatterns as $pattern) {
            if (\mb_strpos($word, $pattern) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Find locations
     *
     * @return array<array{entity: string, type: string, position: int}>
     */
    private function findLocations(string $text): array
    {
        $entities = [];
        $words = $this->tokenize($text);

        for ($i = 0; $i < \count($words); $i++) {
            $word = $words[$i];
            $normalized = $this->normalize($word);

            // Check location indicators
            if (\in_array($word, Config::LOCATION_INDICATORS, true) && isset($words[$i + 1])) {
                $location = $word . ' ' . $words[$i + 1];
                $position = \mb_strpos($text, $location);
                if ($position !== false) {
                    $entities[] = [
                        'entity' => $location,
                        'type' => Config::TYPE_LOCATION,
                        'position' => $position,
                    ];
                }
            } elseif (isset($this->locationsLookup[$normalized])) {
                $position = \mb_strpos($text, $word);
                if ($position !== false) {
                    $entities[] = [
                        'entity' => $word,
                        'type' => Config::TYPE_LOCATION,
                        'position' => $position,
                    ];
                }
            }
        }

        return $entities;
    }

    /**
     * Find organizations
     *
     * @return array<array{entity: string, type: string, position: int}>
     */
    private function findOrganizations(string $text): array
    {
        $entities = [];

        // Check for organization indicators followed by names
        foreach (Config::ORGANIZATION_INDICATORS as $indicator) {
            $pattern = '/' . \preg_quote($indicator, '/') . '\s+[\p{Arabic}\s]+/u';
            if (\preg_match_all($pattern, $text, $matches, \PREG_OFFSET_CAPTURE)) {
                foreach ($matches[0] as $match) {
                    $orgName = \trim($match[0]);
                    // Limit to reasonable length
                    $words = \explode(' ', $orgName);
                    if (\count($words) <= 5) {
                        $entities[] = [
                            'entity' => $orgName,
                            'type' => Config::TYPE_ORGANIZATION,
                            'position' => $match[1],
                        ];
                    }
                }
            }
        }

        // Check known organizations
        foreach (Config::COMMON_ORGANIZATIONS as $org) {
            $position = \mb_strpos($text, $org);
            if ($position !== false) {
                $entities[] = [
                    'entity' => $org,
                    'type' => Config::TYPE_ORGANIZATION,
                    'position' => $position,
                ];
            }
        }

        return $entities;
    }

    /**
     * Find dates
     *
     * @return array<array{entity: string, type: string, position: int}>
     */
    private function findDates(string $text): array
    {
        $entities = [];

        // Numeric date pattern (e.g., 2024/01/15 or 15-01-2024)
        $pattern = '/\d{1,4}[-\/]\d{1,2}[-\/]\d{1,4}/u';
        if (\preg_match_all($pattern, $text, $matches, \PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $entities[] = [
                    'entity' => $match[0],
                    'type' => Config::TYPE_DATE,
                    'position' => $match[1],
                ];
            }
        }

        // Arabic date with months
        $months = \array_merge(Config::DATE_MONTHS_HIJRI, Config::DATE_MONTHS_GREGORIAN);
        foreach ($months as $month) {
            $pattern = '/\d{1,2}\s+' . \preg_quote($month, '/') . '(\s+\d{4})?/u';
            if (\preg_match_all($pattern, $text, $matches, \PREG_OFFSET_CAPTURE)) {
                foreach ($matches[0] as $match) {
                    $entities[] = [
                        'entity' => \trim($match[0]),
                        'type' => Config::TYPE_DATE,
                        'position' => $match[1],
                    ];
                }
            }
        }

        return $entities;
    }

    /**
     * Find emails
     *
     * @return array<array{entity: string, type: string, position: int}>
     */
    private function findEmails(string $text): array
    {
        $entities = [];
        $pattern = '/[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}/u';

        if (\preg_match_all($pattern, $text, $matches, \PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $entities[] = [
                    'entity' => $match[0],
                    'type' => Config::TYPE_EMAIL,
                    'position' => $match[1],
                ];
            }
        }

        return $entities;
    }

    /**
     * Find URLs
     *
     * @return array<array{entity: string, type: string, position: int}>
     */
    private function findUrls(string $text): array
    {
        $entities = [];
        $pattern = '/https?:\/\/[^\s<>"{}|\\^`\[\]]+/u';

        if (\preg_match_all($pattern, $text, $matches, \PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $entities[] = [
                    'entity' => $match[0],
                    'type' => Config::TYPE_URL,
                    'position' => $match[1],
                ];
            }
        }

        return $entities;
    }

    /**
     * Find phone numbers
     *
     * @return array<array{entity: string, type: string, position: int}>
     */
    private function findPhones(string $text): array
    {
        $entities = [];
        // Various phone formats
        $patterns = [
            '/\+?\d{1,3}[-.\s]?\d{2,4}[-.\s]?\d{3,4}[-.\s]?\d{3,4}/u',
            '/0\d{9,11}/u',
        ];

        foreach ($patterns as $pattern) {
            if (\preg_match_all($pattern, $text, $matches, \PREG_OFFSET_CAPTURE)) {
                foreach ($matches[0] as $match) {
                    $phone = \trim($match[0]);
                    if (\mb_strlen(\preg_replace('/\D/', '', $phone) ?? '') >= 9) {
                        $entities[] = [
                            'entity' => $phone,
                            'type' => Config::TYPE_PHONE,
                            'position' => $match[1],
                        ];
                    }
                }
            }
        }

        return $entities;
    }

    /**
     * Find money/currency mentions
     *
     * @return array<array{entity: string, type: string, position: int}>
     */
    private function findMoney(string $text): array
    {
        $entities = [];

        foreach (Config::CURRENCY_INDICATORS as $currency) {
            // Number followed by currency
            $pattern = '/[\d,]+(?:\.\d+)?\s*' . \preg_quote($currency, '/') . '/u';
            if (\preg_match_all($pattern, $text, $matches, \PREG_OFFSET_CAPTURE)) {
                foreach ($matches[0] as $match) {
                    $entities[] = [
                        'entity' => \trim($match[0]),
                        'type' => Config::TYPE_MONEY,
                        'position' => $match[1],
                    ];
                }
            }

            // Currency followed by number
            $pattern = '/' . \preg_quote($currency, '/') . '\s*[\d,]+(?:\.\d+)?/u';
            if (\preg_match_all($pattern, $text, $matches, \PREG_OFFSET_CAPTURE)) {
                foreach ($matches[0] as $match) {
                    $entities[] = [
                        'entity' => \trim($match[0]),
                        'type' => Config::TYPE_MONEY,
                        'position' => $match[1],
                    ];
                }
            }
        }

        return $entities;
    }

    /**
     * Find percentages
     *
     * @return array<array{entity: string, type: string, position: int}>
     */
    private function findPercentages(string $text): array
    {
        $entities = [];
        $pattern = '/\d+(?:\.\d+)?\s*[%٪]/u';

        if (\preg_match_all($pattern, $text, $matches, \PREG_OFFSET_CAPTURE)) {
            foreach ($matches[0] as $match) {
                $entities[] = [
                    'entity' => \trim($match[0]),
                    'type' => Config::TYPE_PERCENTAGE,
                    'position' => $match[1],
                ];
            }
        }

        return $entities;
    }

    /**
     * Tokenize text
     *
     * @return array<string>
     */
    private function tokenize(string $text): array
    {
        $words = \preg_split('/[\s،؛:.!?؟\-\(\)\[\]«»"]+/u', $text, -1, \PREG_SPLIT_NO_EMPTY);

        return $words !== false ? $words : [];
    }

    /**
     * Get entity count by type
     *
     * @return array<string, int>
     */
    public function countByType(string $text): array
    {
        $entities = $this->extract($text);
        $counts = [];

        foreach (Config::VALID_TYPES as $type) {
            $counts[$type] = 0;
        }

        foreach ($entities as $entity) {
            $counts[$entity['type']]++;
        }

        return $counts;
    }

    /**
     * Add custom names
     *
     * @param array<string> $names
     */
    public function addNames(array $names): void
    {
        foreach ($names as $name) {
            $this->namesLookup[$this->normalize($name)] = true;
        }
    }

    /**
     * Add custom locations
     *
     * @param array<string> $locations
     */
    public function addLocations(array $locations): void
    {
        foreach ($locations as $location) {
            $this->locationsLookup[$this->normalize($location)] = true;
        }
    }

    /**
     * Add custom organizations
     *
     * @param array<string> $organizations
     */
    public function addOrganizations(array $organizations): void
    {
        foreach ($organizations as $org) {
            $this->organizationsLookup[$this->normalize($org)] = true;
        }
    }
}
