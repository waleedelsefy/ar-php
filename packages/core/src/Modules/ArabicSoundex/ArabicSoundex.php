<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\ArabicSoundex;

/**
 * ArabicSoundex Facade - PHP 8.4
 *
 * Static facade for easy access to Arabic Soundex functionality.
 *
 * Usage:
 *   use ArPHP\Core\Modules\ArabicSoundex\ArabicSoundex;
 *
 *   $code = ArabicSoundex::soundex('محمد');
 *   $similar = ArabicSoundex::soundsLike('محمد', 'محمود');
 *   $score = ArabicSoundex::similarity('أحمد', 'احمد');
 *
 * @package ArPHP\Core\Modules\ArabicSoundex
 */
final class ArabicSoundex
{
    private static ?ArabicSoundexModule $instance = null;

    private function __construct()
    {
    }

    public static function getInstance(): ArabicSoundexModule
    {
        if (self::$instance === null) {
            self::$instance = new ArabicSoundexModule();
            self::$instance->register();
        }

        return self::$instance;
    }

    /**
     * Configure the module
     *
     * @param array{
     *     code_length?: int,
     *     use_extended?: bool
     * } $config
     */
    public static function configure(array $config): ArabicSoundexModule
    {
        self::$instance = new ArabicSoundexModule($config);
        self::$instance->register();

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
     * Generate Arabic Soundex code
     *
     * Example:
     *   ArabicSoundex::soundex('محمد') // "JFJA"
     *   ArabicSoundex::soundex('محمود') // "JFJA"
     */
    public static function soundex(string $word): string
    {
        return self::getInstance()->soundex($word);
    }

    /**
     * Generate Arabic Metaphone code
     *
     * Example:
     *   ArabicSoundex::metaphone('محمد') // "MHMD"
     *   ArabicSoundex::metaphone('أحمد') // "AHMD"
     */
    public static function metaphone(string $word): string
    {
        return self::getInstance()->metaphone($word);
    }

    /**
     * Romanize Arabic text to Latin pronunciation
     *
     * Example:
     *   ArabicSoundex::romanize('محمد') // "Muhammad"
     *   ArabicSoundex::romanize('أحمد') // "Ahmad"
     *   ArabicSoundex::romanize('عبدالله') // "Abdullah"
     *   ArabicSoundex::romanize('فاطمة') // "Fatima"
     *
     * @param string $word Arabic word
     * @param bool $simple Use simple ASCII-only romanization (default: true)
     * @return string Romanized pronunciation
     */
    public static function romanize(string $word, bool $simple = true): string
    {
        return self::getInstance()->romanize($word, $simple);
    }

    /**
     * Check if two words sound similar
     *
     * Example:
     *   ArabicSoundex::soundsLike('محمد', 'محمود') // true
     *   ArabicSoundex::soundsLike('أحمد', 'محمد') // false
     */
    public static function soundsLike(string $word1, string $word2): bool
    {
        return self::getInstance()->soundsLike($word1, $word2);
    }

    /**
     * Get similarity score between two words (0-100)
     *
     * Example:
     *   ArabicSoundex::similarity('أحمد', 'احمد') // 95
     *   ArabicSoundex::similarity('محمد', 'أحمد') // 75
     */
    public static function similarity(string $word1, string $word2): int
    {
        return self::getInstance()->similarity($word1, $word2);
    }

    /**
     * Find similar words from a list
     *
     * Example:
     *   $names = ['محمد', 'محمود', 'أحمد', 'خالد'];
     *   ArabicSoundex::findSimilar('محمد', $names, 70);
     *   // ['محمد' => 100, 'محمود' => 85]
     *
     * @param array<string> $wordList
     * @return array<string, int>
     */
    public static function findSimilar(string $word, array $wordList, int $threshold = 70): array
    {
        return self::getInstance()->findSimilar($word, $wordList, $threshold);
    }

    /**
     * Get phonetic variants of a word
     *
     * Example:
     *   ArabicSoundex::variants('أحمد');
     *   // ['أحمد', 'احمد', 'إحمد', 'آحمد', 'عحمد']
     *
     * @return array<string>
     */
    public static function variants(string $word): array
    {
        return self::getInstance()->variants($word);
    }

    /**
     * Get phonetic key for database indexing
     *
     * Example:
     *   ArabicSoundex::phoneticKey('محمد') // "JFJA-MHMD"
     */
    public static function phoneticKey(string $word): string
    {
        return self::getInstance()->phoneticKey($word);
    }

    /**
     * Check if word matches a phonetic pattern
     */
    public static function matchesPattern(string $word, string $pattern): bool
    {
        return self::getInstance()->matchesPattern($word, $pattern);
    }

    /**
     * Set code length
     */
    public static function setCodeLength(int $length): ArabicSoundexModule
    {
        return self::getInstance()->setCodeLength($length);
    }

    /**
     * Enable extended mode for finer phonetic distinction
     */
    public static function setExtendedMode(bool $extended): ArabicSoundexModule
    {
        return self::getInstance()->setExtendedMode($extended);
    }

    /**
     * Compare multiple words and return match groups
     *
     * @param array<string> $words
     * @return array<string, array<string>>
     */
    public static function groupBySoundex(array $words): array
    {
        $groups = [];

        foreach ($words as $word) {
            $code = self::soundex($word);

            if (!isset($groups[$code])) {
                $groups[$code] = [];
            }

            $groups[$code][] = $word;
        }

        return $groups;
    }

    /**
     * Check if a name could be a variant spelling
     */
    public static function isNameVariant(string $name1, string $name2): bool
    {
        // Names are considered variants if similarity >= 80
        return self::similarity($name1, $name2) >= 80;
    }

    /**
     * Fuzzy search in array
     *
     * @param array<string> $haystack
     * @return array<string>
     */
    public static function fuzzySearch(string $needle, array $haystack, int $threshold = 60): array
    {
        $results = self::findSimilar($needle, $haystack, $threshold);
        return \array_keys($results);
    }
}
