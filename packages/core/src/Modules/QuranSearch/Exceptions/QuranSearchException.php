<?php

declare(strict_types=1);

namespace ArPHP\Core\Modules\QuranSearch\Exceptions;

use ArPHP\Core\Exceptions\ArPHPException;

/**
 * Quran Search Exception - PHP 8.4
 *
 * @package ArPHP\Core\Modules\QuranSearch
 */
class QuranSearchException extends ArPHPException
{
    public static function invalidSurah(int $surah): self
    {
        return new self("Invalid surah number: {$surah}. Must be between 1 and 114.");
    }

    public static function invalidAyah(int $surah, int $ayah): self
    {
        return new self("Invalid ayah number {$ayah} for surah {$surah}.");
    }

    public static function emptyQuery(): self
    {
        return new self('Search query cannot be empty.');
    }

    public static function dataNotLoaded(): self
    {
        return new self('Quran data not loaded. Please ensure data files are available.');
    }

    public static function invalidRange(int $from, int $to): self
    {
        return new self("Invalid range: from {$from} to {$to}.");
    }

    public static function rootNotFound(string $root): self
    {
        return new self("Root '{$root}' not found in database.");
    }
}
