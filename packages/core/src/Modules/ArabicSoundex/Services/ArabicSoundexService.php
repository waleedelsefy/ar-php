<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\ArabicSoundex\Services;

use ArPHP\Core\Contracts\ServiceInterface;
use ArPHP\Core\Modules\ArabicSoundex\Config;
use ArPHP\Core\Modules\ArabicSoundex\Contracts\ArabicSoundexInterface;
use ArPHP\Core\Modules\ArabicSoundex\Exceptions\ArabicSoundexException;

/**
 * Arabic Soundex Service - PHP 8.4
 *
 * Implements phonetic algorithms for Arabic text matching.
 *
 * @package ArPHP\Core\Modules\ArabicSoundex\Services
 */
final class ArabicSoundexService implements ArabicSoundexInterface, ServiceInterface
{
    /** @var array<string, array<string, mixed>>|null */
    private ?array $namesDatabase = null;
    
    public function __construct(
        private int $codeLength = Config::DEFAULT_CODE_LENGTH,
        private bool $useExtended = false
    ) {
        if ($codeLength < 1) {
            throw ArabicSoundexException::invalidCodeLength($codeLength);
        }
    }

    public function getServiceName(): string
    {
        return 'arabic_soundex';
    }

    /**
     * @return array<string, mixed>
     */
    public function getConfig(): array
    {
        return [
            'code_length' => $this->codeLength,
            'use_extended' => $this->useExtended,
        ];
    }

    public function isAvailable(): bool
    {
        return true;
    }

    public function soundex(string $word): string
    {
        $word = $this->normalizeWord($word);

        if ($word === '') {
            return '';
        }

        $map = $this->useExtended ? Config::SOUNDEX_EXTENDED : Config::SOUNDEX_MAP;
        $chars = $this->mbStrSplit($word);

        // Keep first letter
        $firstChar = $chars[0];
        $code = $map[$firstChar] ?? $firstChar;

        $prevCode = $code;
        $result = $code;

        // Process remaining characters
        for ($i = 1; $i < \count($chars); $i++) {
            $char = $chars[$i];
            $charCode = $map[$char] ?? '';

            // Skip if same as previous code (avoid duplicates)
            if ($charCode !== '' && $charCode !== $prevCode) {
                $result .= $charCode;
                $prevCode = $charCode;
            }

            // Stop if we have enough characters
            if (\strlen($result) >= $this->codeLength) {
                break;
            }
        }

        // Pad with zeros if needed
        return \str_pad($result, $this->codeLength, '0');
    }

    public function metaphone(string $word): string
    {
        $word = $this->normalizeWord($word);

        if ($word === '') {
            return '';
        }

        $chars = $this->mbStrSplit($word);
        $result = '';

        foreach ($chars as $char) {
            $result .= Config::METAPHONE_MAP[$char] ?? '';
        }

        // Remove consecutive duplicate characters
        $result = \preg_replace('/(.)\1+/', '$1', $result) ?? $result;

        return $result;
    }

    /**
     * Romanize Arabic text to Latin script
     * Produces readable pronunciation like "Muhammad" for "محمد"
     */
    public function romanize(string $word, bool $simple = true): string
    {
        if ($word === '') {
            return '';
        }

        // Check for common names first
        $commonName = $this->getCommonNameRomanization($word);
        if ($commonName !== null) {
            return $commonName;
        }

        // Keep original for diacritics processing
        $originalWord = $word;
        
        // Get diacritics positions before normalization
        $diacriticsInfo = $this->extractDiacritics($originalWord);
        
        // Normalize but don't remove diacritics yet
        $word = \str_replace(['أ', 'إ', 'آ'], 'ا', $word);
        $word = \str_replace('ى', 'ي', $word);
        
        // Remove diacritics for character processing
        $cleanWord = \str_replace(Config::DIACRITICS, '', $word);
        
        // Remove non-Arabic
        $cleanWord = \preg_replace('/[^\x{0600}-\x{06FF}]/u', '', $cleanWord) ?? '';
        
        if ($cleanWord === '') {
            return '';
        }

        $map = $simple ? Config::ROMANIZATION_SIMPLE : Config::ROMANIZATION_MAP;
        $chars = $this->mbStrSplit($cleanWord);
        $result = '';
        $len = \count($chars);

        for ($i = 0; $i < $len; $i++) {
            $char = $chars[$i];
            $nextChar = $chars[$i + 1] ?? null;
            $prevChar = $chars[$i - 1] ?? null;
            
            // Get base romanization
            $roman = $map[$char] ?? $char;
            
            // Smart vowel insertion based on position and context
            $vowelInfo = $this->getVowelInfo($char, $prevChar, $nextChar, $i, $len, $diacriticsInfo, $chars);
            
            if ($vowelInfo['needs_vowel']) {
                $result .= $roman . $vowelInfo['vowel'];
            } else {
                $result .= $roman;
            }
        }

        // Clean up
        $result = \preg_replace('/([aeiou])\1+/', '$1', $result) ?? $result; // Remove duplicate vowels
        $result = \trim($result);
        
        // Capitalize first letter
        return \ucfirst($result);
    }

    /**
     * Load names database from JSON file
     */
    private function loadNamesDatabase(): void
    {
        if ($this->namesDatabase !== null) {
            return;
        }
        
        $jsonPath = __DIR__ . '/../Data/arabic_names.json';
        
        if (\file_exists($jsonPath)) {
            $content = \file_get_contents($jsonPath);
            if ($content !== false) {
                $data = \json_decode($content, true);
                if (\is_array($data)) {
                    $this->namesDatabase = $data;
                    return;
                }
            }
        }
        
        // Fallback to empty database
        $this->namesDatabase = [
            'male_names' => [],
            'female_names' => [],
            'common_words' => []
        ];
    }

    /**
     * Get romanization for common Arabic names from JSON database
     */
    private function getCommonNameRomanization(string $word): ?string
    {
        // Load database if not loaded
        $this->loadNamesDatabase();
        
        // Normalize the word for lookup
        $normalized = \str_replace(Config::DIACRITICS, '', $word);
        $normalized = \str_replace(['أ', 'إ', 'آ'], 'ا', $normalized);
        $normalized = \str_replace('ة', 'ه', $normalized);
        
        // Also try with ة kept as is
        $normalizedWithTa = \str_replace(Config::DIACRITICS, '', $word);
        $normalizedWithTa = \str_replace(['أ', 'إ', 'آ'], 'ا', $normalizedWithTa);
        
        // Search in all categories
        $categories = ['male_names', 'female_names', 'common_words'];
        
        foreach ($categories as $category) {
            if (!isset($this->namesDatabase[$category])) {
                continue;
            }
            
            // Try original word
            if (isset($this->namesDatabase[$category][$word])) {
                return $this->namesDatabase[$category][$word]['roman'];
            }
            
            // Try normalized
            if (isset($this->namesDatabase[$category][$normalized])) {
                return $this->namesDatabase[$category][$normalized]['roman'];
            }
            
            // Try with ta marbuta
            if (isset($this->namesDatabase[$category][$normalizedWithTa])) {
                return $this->namesDatabase[$category][$normalizedWithTa]['roman'];
            }
        }
        
        return null;
    }

    /**
     * Get vowel information for a character
     * 
     * @return array{needs_vowel: bool, vowel: string}
     */
    private function getVowelInfo(string $char, ?string $prevChar, ?string $nextChar, int $pos, int $len, array $diacritics, array $allChars): array
    {
        // Don't add vowel after vowel letters (ا و ي ة)
        if (\in_array($char, ['ا', 'ى', 'ة'], true)) {
            return ['needs_vowel' => false, 'vowel' => ''];
        }
        
        // و and ي can be vowels or consonants
        if ($char === 'و') {
            // If و is at end or followed by consonant, it's a vowel (oo/ou)
            if ($nextChar === null || !\in_array($nextChar, ['ا', 'و', 'ي', 'ى'], true)) {
                return ['needs_vowel' => false, 'vowel' => ''];
            }
        }
        
        if ($char === 'ي') {
            // If ي is at end, it's usually vowel
            if ($nextChar === null) {
                return ['needs_vowel' => false, 'vowel' => ''];
            }
        }
        
        // If we have explicit diacritic, use it
        if (isset($diacritics[$pos])) {
            return ['needs_vowel' => true, 'vowel' => $diacritics[$pos]];
        }
        
        // Last character usually doesn't need vowel
        if ($pos === $len - 1) {
            return ['needs_vowel' => false, 'vowel' => ''];
        }
        
        // Check if next is a vowel letter (long vowel)
        if (\in_array($nextChar, ['ا', 'و', 'ي', 'ى'], true)) {
            return ['needs_vowel' => false, 'vowel' => ''];
        }
        
        // Determine the vowel based on context
        $vowel = $this->guessSmartVowel($char, $nextChar, $pos, $len, $allChars);
        
        return ['needs_vowel' => true, 'vowel' => $vowel];
    }

    /**
     * Smart vowel guessing based on Arabic phonotactics
     */
    private function guessSmartVowel(string $char, ?string $nextChar, int $pos, int $len, array $allChars): string
    {
        // Emphatic consonants (ص ض ط ظ ق) often take 'a' or 'u'
        $emphaticChars = ['ص', 'ض', 'ط', 'ظ', 'ق'];
        if (\in_array($char, $emphaticChars, true)) {
            return 'a';
        }
        
        // Before double consonant or shadda context, use 'a'
        if ($nextChar !== null && $nextChar === ($allChars[$pos + 2] ?? null)) {
            return 'a';
        }
        
        // م before ح often uses 'u' (like in محمد = muhammad)
        if ($char === 'م' && $nextChar === 'ح') {
            return 'u';
        }
        
        // ن before ص often uses 'a' (like in منصور)
        if ($char === 'ن' && $nextChar === 'ص') {
            return 'a';
        }
        
        // First position often uses 'a' in names
        if ($pos === 0) {
            return 'a';
        }
        
        // Default to 'a' (most common)
        return 'a';
    }

    /**
     * Extract diacritics information from word
     * 
     * @return array<int, string>
     */
    private function extractDiacritics(string $word): array
    {
        $diacritics = [];
        $chars = $this->mbStrSplit($word);
        $consonantIndex = -1;
        
        foreach ($chars as $char) {
            // Check if it's a diacritic
            if (\in_array($char, Config::DIACRITICS, true)) {
                if ($consonantIndex >= 0 && isset(Config::VOWEL_PATTERNS[$char])) {
                    $diacritics[$consonantIndex] = Config::VOWEL_PATTERNS[$char];
                }
            } elseif (\preg_match('/[\x{0600}-\x{06FF}]/u', $char)) {
                // It's a consonant/letter
                $consonantIndex++;
            }
        }
        
        return $diacritics;
    }

    public function soundsLike(string $word1, string $word2): bool
    {
        $code1 = $this->soundex($word1);
        $code2 = $this->soundex($word2);

        return $code1 === $code2;
    }

    public function similarity(string $word1, string $word2): int
    {
        $word1 = $this->normalizeWord($word1);
        $word2 = $this->normalizeWord($word2);

        if ($word1 === '' || $word2 === '') {
            return 0;
        }

        if ($word1 === $word2) {
            return 100;
        }

        // Combine multiple similarity metrics
        $soundexSimilarity = $this->soundexSimilarity($word1, $word2);
        $metaphoneSimilarity = $this->metaphoneSimilarity($word1, $word2);
        $levenshteinSimilarity = $this->levenshteinSimilarity($word1, $word2);

        // Weighted average (Soundex: 40%, Metaphone: 35%, Levenshtein: 25%)
        $score = ($soundexSimilarity * 0.40) + ($metaphoneSimilarity * 0.35) + ($levenshteinSimilarity * 0.25);

        return (int) \round($score);
    }

    /**
     * @return array<string, int>
     */
    public function findSimilar(string $word, array $wordList, int $threshold = 70): array
    {
        if ($threshold < 0 || $threshold > 100) {
            throw ArabicSoundexException::invalidThreshold($threshold);
        }

        $results = [];
        $targetCode = $this->soundex($word);

        foreach ($wordList as $candidate) {
            $similarity = $this->similarity($word, $candidate);

            if ($similarity >= $threshold) {
                $results[$candidate] = $similarity;
            }
        }

        // Sort by similarity descending
        \arsort($results);

        return $results;
    }

    /**
     * @return array<string>
     */
    public function getPhoneticVariants(string $word): array
    {
        $word = $this->normalizeWord($word);

        if ($word === '') {
            return [];
        }

        $chars = $this->mbStrSplit($word);
        $variants = [$word];

        foreach ($chars as $index => $char) {
            // Find similar characters
            foreach (Config::SIMILAR_GROUPS as $group) {
                if (\in_array($char, $group, true)) {
                    foreach ($group as $variant) {
                        if ($variant !== $char) {
                            $newChars = $chars;
                            $newChars[$index] = $variant;
                            $variants[] = \implode('', $newChars);
                        }
                    }
                    break;
                }
            }
        }

        return \array_unique($variants);
    }

    /**
     * Generate soundex code with custom length
     */
    public function soundexWithLength(string $word, int $length): string
    {
        $originalLength = $this->codeLength;
        $this->codeLength = $length;
        $result = $this->soundex($word);
        $this->codeLength = $originalLength;

        return $result;
    }

    /**
     * Get the phonetic key for indexing
     */
    public function getPhoneticKey(string $word): string
    {
        return $this->soundex($word) . '-' . $this->metaphone($word);
    }

    /**
     * Check if word matches a pattern phonetically
     */
    public function matchesPattern(string $word, string $pattern): bool
    {
        $wordCode = $this->soundex($word);
        $patternCode = $this->soundex($pattern);

        // Check if pattern matches start of word code
        return \str_starts_with($wordCode, \rtrim($patternCode, '0'));
    }

    /**
     * Set code length
     */
    public function setCodeLength(int $length): self
    {
        if ($length < 1) {
            throw ArabicSoundexException::invalidCodeLength($length);
        }

        $this->codeLength = $length;
        return $this;
    }

    /**
     * Set extended mode
     */
    public function setExtendedMode(bool $extended): self
    {
        $this->useExtended = $extended;
        return $this;
    }

    /**
     * Normalize Arabic word for processing
     */
    private function normalizeWord(string $word): string
    {
        // Remove diacritics
        $word = \str_replace(Config::DIACRITICS, '', $word);

        // Remove non-Arabic characters
        $word = \preg_replace('/[^\x{0600}-\x{06FF}]/u', '', $word) ?? '';

        // Normalize Alef variants
        $word = \str_replace(['أ', 'إ', 'آ'], 'ا', $word);

        // Normalize Yaa
        $word = \str_replace('ى', 'ي', $word);

        // Normalize Taa Marbuta (at end of word)
        if (\mb_substr($word, -1) === 'ة') {
            $word = \mb_substr($word, 0, -1) . 'ه';
        }

        return \trim($word);
    }

    /**
     * Calculate Soundex similarity (0-100)
     */
    private function soundexSimilarity(string $word1, string $word2): float
    {
        $code1 = $this->soundex($word1);
        $code2 = $this->soundex($word2);

        if ($code1 === $code2) {
            return 100.0;
        }

        $matches = 0;
        $len = \min(\strlen($code1), \strlen($code2));

        for ($i = 0; $i < $len; $i++) {
            if ($code1[$i] === $code2[$i]) {
                $matches++;
            }
        }

        return ($matches / $this->codeLength) * 100;
    }

    /**
     * Calculate Metaphone similarity (0-100)
     */
    private function metaphoneSimilarity(string $word1, string $word2): float
    {
        $meta1 = $this->metaphone($word1);
        $meta2 = $this->metaphone($word2);

        if ($meta1 === $meta2) {
            return 100.0;
        }

        if ($meta1 === '' || $meta2 === '') {
            return 0.0;
        }

        $maxLen = \max(\strlen($meta1), \strlen($meta2));
        $distance = \levenshtein($meta1, $meta2);

        return \max(0, (1 - ($distance / $maxLen)) * 100);
    }

    /**
     * Calculate Levenshtein similarity for Arabic (0-100)
     */
    private function levenshteinSimilarity(string $word1, string $word2): float
    {
        $len1 = \mb_strlen($word1);
        $len2 = \mb_strlen($word2);
        $maxLen = \max($len1, $len2);

        if ($maxLen === 0) {
            return 100.0;
        }

        // Use custom Arabic-aware Levenshtein
        $distance = $this->arabicLevenshtein($word1, $word2);

        return \max(0, (1 - ($distance / $maxLen)) * 100);
    }

    /**
     * Arabic-aware Levenshtein distance
     */
    private function arabicLevenshtein(string $str1, string $str2): int
    {
        $chars1 = $this->mbStrSplit($str1);
        $chars2 = $this->mbStrSplit($str2);

        $len1 = \count($chars1);
        $len2 = \count($chars2);

        if ($len1 === 0) {
            return $len2;
        }
        if ($len2 === 0) {
            return $len1;
        }

        $matrix = [];

        for ($i = 0; $i <= $len1; $i++) {
            $matrix[$i][0] = $i;
        }
        for ($j = 0; $j <= $len2; $j++) {
            $matrix[0][$j] = $j;
        }

        for ($i = 1; $i <= $len1; $i++) {
            for ($j = 1; $j <= $len2; $j++) {
                $cost = $chars1[$i - 1] === $chars2[$j - 1] ? 0 : 1;

                // Reduce cost for similar sounding characters
                if ($cost === 1 && $this->areSimilarSounds($chars1[$i - 1], $chars2[$j - 1])) {
                    $cost = 0.5;
                }

                $matrix[$i][$j] = \min(
                    $matrix[$i - 1][$j] + 1,      // deletion
                    $matrix[$i][$j - 1] + 1,      // insertion
                    $matrix[$i - 1][$j - 1] + $cost // substitution
                );
            }
        }

        return (int) \ceil($matrix[$len1][$len2]);
    }

    /**
     * Check if two characters are in the same phonetic group
     */
    private function areSimilarSounds(string $char1, string $char2): bool
    {
        foreach (Config::SIMILAR_GROUPS as $group) {
            if (\in_array($char1, $group, true) && \in_array($char2, $group, true)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Split multibyte string into array of characters
     *
     * @return array<string>
     */
    private function mbStrSplit(string $string): array
    {
        return \preg_split('//u', $string, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    }
}
