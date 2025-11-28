<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\Lemmatizer\Services;

use ArPHP\Core\Modules\Lemmatizer\Config;
use ArPHP\Core\Modules\Lemmatizer\Contracts\LemmatizerInterface;

/**
 * Lemmatizer Service - PHP 8.4
 *
 * Arabic word stemming and lemmatization.
 *
 * @package ArPHP\Core\Modules\Lemmatizer
 */
final class LemmatizerService implements LemmatizerInterface
{
    /** @var array<string> Sorted prefixes by length (longest first) */
    private array $sortedPrefixes;

    /** @var array<string> Sorted suffixes by length (longest first) */
    private array $sortedSuffixes;

    public function __construct()
    {
        // Sort prefixes by length (longest first) for proper matching
        $this->sortedPrefixes = Config::PREFIXES;
        \usort($this->sortedPrefixes, fn($a, $b) => \mb_strlen($b) - \mb_strlen($a));

        // Sort suffixes by length (longest first)
        $this->sortedSuffixes = Config::SUFFIXES;
        \usort($this->sortedSuffixes, fn($a, $b) => \mb_strlen($b) - \mb_strlen($a));
    }

    /**
     * @inheritDoc
     */
    public function lemmatize(string $word): string
    {
        $word = $this->normalize($word);

        if (\mb_strlen($word) < Config::MIN_WORD_LENGTH) {
            return $word;
        }

        // Remove affixes to get base form
        return $this->removeAffixes($word);
    }

    /**
     * @inheritDoc
     */
    public function lemmatizeText(string $text): string
    {
        $words = \preg_split('/\s+/u', $text, -1, \PREG_SPLIT_NO_EMPTY);

        if ($words === false) {
            return $text;
        }

        $lemmatized = \array_map(fn($word) => $this->lemmatize($word), $words);

        return \implode(' ', $lemmatized);
    }

    /**
     * @inheritDoc
     */
    public function getRoot(string $word): string
    {
        $word = $this->normalize($word);
        $stem = $this->stem($word);

        // Extract root letters
        return $this->extractRoot($stem);
    }

    /**
     * @inheritDoc
     */
    public function stem(string $word): string
    {
        $word = $this->normalize($word);

        if (\mb_strlen($word) < Config::MIN_WORD_LENGTH) {
            return $word;
        }

        // Light stemming - just remove common affixes
        return $this->removeAffixes($word);
    }

    /**
     * @inheritDoc
     */
    public function removePrefix(string $word): string
    {
        $word = $this->normalize($word);

        foreach ($this->sortedPrefixes as $prefix) {
            $prefixLen = \mb_strlen($prefix);
            if (\mb_strlen($word) > $prefixLen + 1) {
                if (\mb_substr($word, 0, $prefixLen) === $prefix) {
                    return \mb_substr($word, $prefixLen);
                }
            }
        }

        return $word;
    }

    /**
     * @inheritDoc
     */
    public function removeSuffix(string $word): string
    {
        $word = $this->normalize($word);

        foreach ($this->sortedSuffixes as $suffix) {
            $suffixLen = \mb_strlen($suffix);
            if (\mb_strlen($word) > $suffixLen + 1) {
                if (\mb_substr($word, -$suffixLen) === $suffix) {
                    return \mb_substr($word, 0, -$suffixLen);
                }
            }
        }

        return $word;
    }

    /**
     * @inheritDoc
     */
    public function removeAffixes(string $word): string
    {
        // First remove prefix
        $word = $this->removePrefix($word);

        // Then remove suffix
        $word = $this->removeSuffix($word);

        return $word;
    }

    /**
     * @inheritDoc
     */
    public function hasPrefix(string $word): bool
    {
        $word = $this->normalize($word);

        foreach ($this->sortedPrefixes as $prefix) {
            if (\mb_strlen($word) > \mb_strlen($prefix) + 1) {
                if (\mb_substr($word, 0, \mb_strlen($prefix)) === $prefix) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function hasSuffix(string $word): bool
    {
        $word = $this->normalize($word);

        foreach ($this->sortedSuffixes as $suffix) {
            if (\mb_strlen($word) > \mb_strlen($suffix) + 1) {
                if (\mb_substr($word, -\mb_strlen($suffix)) === $suffix) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function getPattern(string $word): string
    {
        $word = $this->normalize($word);
        $root = $this->getRoot($word);

        if (\mb_strlen($root) < 3) {
            return $word;
        }

        // Replace root letters with ف ع ل pattern
        $rootLetters = \preg_split('//u', $root, -1, \PREG_SPLIT_NO_EMPTY);
        if ($rootLetters === false || \count($rootLetters) < 3) {
            return $word;
        }

        $patternLetters = ['ف', 'ع', 'ل'];
        $pattern = $word;

        for ($i = 0; $i < \min(\count($rootLetters), 3); $i++) {
            $pattern = \str_replace($rootLetters[$i], $patternLetters[$i], $pattern);
        }

        return $pattern;
    }

    /**
     * Normalize word
     */
    private function normalize(string $word): string
    {
        // Trim whitespace
        $word = \trim($word);

        // Remove diacritics
        $word = \str_replace(Config::DIACRITICS, '', $word);

        // Normalize Alef
        $word = \str_replace(Config::ALEF_VARIANTS, Config::ALEF_NORMAL, $word);

        // Normalize Ta Marbuta to Ha
        $word = \str_replace('ة', 'ه', $word);

        // Normalize Alef Maqsura to Ya
        $word = \str_replace('ى', 'ي', $word);

        return $word;
    }

    /**
     * Extract root letters from stem
     */
    private function extractRoot(string $stem): string
    {
        $letters = \preg_split('//u', $stem, -1, \PREG_SPLIT_NO_EMPTY);
        if ($letters === false) {
            return $stem;
        }

        $rootLetters = [];

        foreach ($letters as $letter) {
            // Check if letter is a root letter (not an extra letter)
            if (!\in_array($letter, Config::EXTRA_LETTERS, true) || \count($rootLetters) < Config::TRILATERAL_ROOT) {
                // Check if letter is in the Arabic alphabet
                if (\mb_strpos(Config::ROOT_LETTERS, $letter) !== false) {
                    $rootLetters[] = $letter;
                } else {
                    // For extra letters, still include if we need more root letters
                    $rootLetters[] = $letter;
                }
            }

            // Stop at 3 letters for trilateral root
            if (\count($rootLetters) >= Config::TRILATERAL_ROOT) {
                break;
            }
        }

        return \implode('', $rootLetters);
    }

    /**
     * Get all possible roots for a word
     *
     * @return array<string>
     */
    public function getPossibleRoots(string $word): array
    {
        $word = $this->normalize($word);
        $roots = [];

        // Get root from different stem forms
        $roots[] = $this->getRoot($word);
        $roots[] = $this->getRoot($this->removePrefix($word));
        $roots[] = $this->getRoot($this->removeSuffix($word));
        $roots[] = $this->getRoot($this->removeAffixes($word));

        // Remove duplicates and empty values
        return \array_values(\array_unique(\array_filter($roots)));
    }

    /**
     * Get morphological analysis
     *
     * @return array{original: string, normalized: string, prefix: string, stem: string, suffix: string, root: string, pattern: string}
     */
    public function analyze(string $word): array
    {
        $original = $word;
        $normalized = $this->normalize($word);
        $withoutPrefix = $this->removePrefix($normalized);
        $prefix = \mb_substr($normalized, 0, \mb_strlen($normalized) - \mb_strlen($withoutPrefix));

        $withoutSuffix = $this->removeSuffix($withoutPrefix);
        $suffix = \mb_substr($withoutPrefix, \mb_strlen($withoutSuffix));

        return [
            'original' => $original,
            'normalized' => $normalized,
            'prefix' => $prefix,
            'stem' => $withoutSuffix,
            'suffix' => $suffix,
            'root' => $this->getRoot($word),
            'pattern' => $this->getPattern($word),
        ];
    }

    /**
     * Check if two words share the same root
     */
    public function shareRoot(string $word1, string $word2): bool
    {
        return $this->getRoot($word1) === $this->getRoot($word2);
    }

    /**
     * Find words with same root
     *
     * @param array<string> $words
     * @return array<string>
     */
    public function findRelatedWords(string $word, array $words): array
    {
        $root = $this->getRoot($word);
        $related = [];

        foreach ($words as $candidate) {
            if ($this->getRoot($candidate) === $root) {
                $related[] = $candidate;
            }
        }

        return $related;
    }

    /**
     * Get detected prefix
     */
    public function getPrefix(string $word): string
    {
        $word = $this->normalize($word);
        $withoutPrefix = $this->removePrefix($word);

        return \mb_substr($word, 0, \mb_strlen($word) - \mb_strlen($withoutPrefix));
    }

    /**
     * Get detected suffix
     */
    public function getSuffix(string $word): string
    {
        $word = $this->normalize($word);
        $stem = $this->removeAffixes($word);
        $withoutPrefix = $this->removePrefix($word);

        return \mb_substr($withoutPrefix, \mb_strlen($stem));
    }
}
