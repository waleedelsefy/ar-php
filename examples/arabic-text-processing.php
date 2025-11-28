<?php

require_once __DIR__ . '/../vendor/autoload.php';

use ArPHP\Core\Arabic;
use ArPHP\Core\Modules\TashkeelModule;
use ArPHP\Core\Modules\TransliterationModule;
use ArPHP\Core\Modules\NumbersModule;
use ArPHP\Core\Modules\TextCleanerModule;

/**
 * Arabic Text Processing Demo
 * 
 * Demonstrates all Arabic text processing modules working together
 */

echo str_repeat('=', 70) . "\n";
echo "🌟 Arabic Text Processing - Complete Demo\n";
echo str_repeat('=', 70) . "\n\n";

// Initialize with all Arabic processing modules
Arabic::init([
    new TashkeelModule(),
    new TransliterationModule(),
    new NumbersModule(),
    new TextCleanerModule(),
]);

echo "✅ All modules loaded successfully!\n\n";

// ============================================
// 1. Tashkeel (Diacritics) Processing
// ============================================

echo str_repeat('=', 70) . "\n";
echo "1️⃣ Tashkeel Processing\n";
echo str_repeat('-', 70) . "\n\n";

$tashkeel = Arabic::container()->get('tashkeel');

$textWithTashkeel = 'بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ';
$textWithoutTashkeel = 'بسم الله الرحمن الرحيم';

echo "Original text: {$textWithTashkeel}\n";
echo "Remove tashkeel: " . $tashkeel->remove($textWithTashkeel) . "\n";
echo "Has tashkeel: " . ($tashkeel->has($textWithTashkeel) ? 'Yes' : 'No') . "\n";
echo "Tashkeel count: " . $tashkeel->count($textWithTashkeel) . "\n\n";

echo "Add tashkeel to common words:\n";
echo "  مرحبا → " . $tashkeel->add('مرحبا') . "\n";
echo "  شكرا → " . $tashkeel->add('شكرا') . "\n";
echo "  السلام عليكم → " . $tashkeel->add('السلام عليكم') . "\n\n";

echo "Normalize text:\n";
echo "  أحمد → " . $tashkeel->normalize('أحمد') . "\n";
echo "  إبراهيم → " . $tashkeel->normalize('إبراهيم') . "\n";
echo "  مدرسة → " . $tashkeel->normalize('مدرسة') . "\n\n";

// ============================================
// 2. Transliteration (Arabic-Latin)
// ============================================

echo str_repeat('=', 70) . "\n";
echo "2️⃣ Transliteration\n";
echo str_repeat('-', 70) . "\n\n";

$trans = Arabic::container()->get('transliteration');

$arabicNames = ['محمد', 'أحمد', 'فاطمة', 'خديجة'];

echo "Arabic to Latin:\n";
foreach ($arabicNames as $name) {
    echo "  {$name} → " . $trans->toLatin($name) . "\n";
}

echo "\nLatin to Arabic:\n";
$latinNames = ['ahmad', 'khalid', 'fatima', 'khadija'];
foreach ($latinNames as $name) {
    echo "  {$name} → " . $trans->toArabic($name) . "\n";
}

echo "\nAuto-detect and convert:\n";
echo "  محمد → " . $trans->convert('محمد') . "\n";
echo "  ahmad → " . $trans->convert('ahmad') . "\n\n";

// ============================================
// 3. Numbers Processing
// ============================================

echo str_repeat('=', 70) . "\n";
echo "3️⃣ Numbers Processing\n";
echo str_repeat('-', 70) . "\n\n";

$numbers = Arabic::container()->get('numbers');

echo "Western to Arabic-Indic:\n";
echo "  123 → " . $numbers->toArabicIndic('123') . "\n";
echo "  2025 → " . $numbers->toArabicIndic('2025') . "\n";
echo "  Phone: 0123456789 → " . $numbers->toArabicIndic('0123456789') . "\n\n";

echo "Arabic-Indic to Western:\n";
echo "  ١٢٣ → " . $numbers->toWestern('١٢٣') . "\n";
echo "  ٢٠٢٥ → " . $numbers->toWestern('٢٠٢٥') . "\n\n";

echo "Numbers to Arabic words:\n";
for ($i = 1; $i <= 10; $i++) {
    echo "  {$i} → " . $numbers->toWords($i) . "\n";
}
echo "  25 → " . $numbers->toWords(25) . "\n";
echo "  100 → " . $numbers->toWords(100) . "\n";
echo "  250 → " . $numbers->toWords(250) . "\n\n";

echo "Format numbers:\n";
echo "  1234567.89 → " . $numbers->format(1234567.89, 2) . "\n";
echo "  1000000 → " . $numbers->format(1000000) . "\n\n";

// ============================================
// 4. Text Cleaning
// ============================================

echo str_repeat('=', 70) . "\n";
echo "4️⃣ Text Cleaning\n";
echo str_repeat('-', 70) . "\n\n";

$cleaner = Arabic::container()->get('text-cleaner');

$dirtyText = "  هذا   نص   عربي    مع    مسافات    كثيرة  ";
echo "Remove extra spaces:\n";
echo "  Before: '{$dirtyText}'\n";
echo "  After: '" . $cleaner->removeExtraSpaces($dirtyText) . "'\n\n";

$htmlText = '<p>نص عربي مع <strong>HTML</strong> tags</p>';
echo "Remove HTML:\n";
echo "  Before: {$htmlText}\n";
echo "  After: " . $cleaner->removeHtml($htmlText) . "\n\n";

$mixedText = 'النص العربي 123 مع English و أرقام 456';
echo "Clean options:\n";
echo "  Original: {$mixedText}\n";
echo "  Remove numbers: " . $cleaner->removeNumbers($mixedText) . "\n";
echo "  Remove English: " . $cleaner->removeEnglish($mixedText) . "\n";
echo "  Arabic only: " . $cleaner->keepArabicOnly($mixedText) . "\n\n";

$urlText = 'زيارة الموقع https://example.com للمزيد';
echo "Remove URLs:\n";
echo "  Before: {$urlText}\n";
echo "  After: " . $cleaner->removeUrls($urlText) . "\n\n";

echo "Comprehensive clean:\n";
$messyText = '<div>  النص  العربي  https://test.com  مع   English123  </div>';
echo "  Before: {$messyText}\n";
$cleaned = $cleaner->clean($messyText, [
    'html' => true,
    'urls' => true,
    'english' => true,
    'numbers' => true,
    'extra_spaces' => true,
]);
echo "  After: {$cleaned}\n\n";

echo "Count words and characters:\n";
$sampleText = 'هذا نص عربي للاختبار';
echo "  Text: {$sampleText}\n";
echo "  Words: " . $cleaner->countWords($sampleText) . "\n";
echo "  Chars: " . $cleaner->countChars($sampleText) . "\n\n";

// ============================================
// 5. Combined Example - Real Use Case
// ============================================

echo str_repeat('=', 70) . "\n";
echo "5️⃣ Combined Real Use Case\n";
echo str_repeat('-', 70) . "\n\n";

$userInput = '<p>  مَرْحَبًا  بك   في  المتجر   السعر  1234  ريال   https://shop.com  </p>';

echo "Original user input:\n{$userInput}\n\n";

echo "Processing pipeline:\n";

// Step 1: Remove HTML
$step1 = $cleaner->removeHtml($userInput);
echo "1. Remove HTML: {$step1}\n";

// Step 2: Remove URLs
$step2 = $cleaner->removeUrls($step1);
echo "2. Remove URLs: {$step2}\n";

// Step 3: Convert numbers to Arabic
$step3 = $numbers->toArabicIndic($step2);
echo "3. Convert numbers: {$step3}\n";

// Step 4: Remove tashkeel for normalization
$step4 = $tashkeel->remove($step3);
echo "4. Remove tashkeel: {$step4}\n";

// Step 5: Clean extra spaces
$step5 = $cleaner->removeExtraSpaces($step4);
echo "5. Clean spaces: {$step5}\n";

// Step 6: Transliterate for search indexing
$step6 = $trans->toLatin($step5);
echo "6. Transliterate: {$step6}\n";

echo "\n✅ Processing complete!\n\n";

// ============================================
// Summary
// ============================================

echo str_repeat('=', 70) . "\n";
echo "📊 Summary\n";
echo str_repeat('=', 70) . "\n\n";

echo "Modules loaded:\n";
foreach (Arabic::registry()->all() as $module) {
    echo "  ✓ " . $module->getName() . " v" . $module->getVersion() . "\n";
}

echo "\nServices available:\n";
$services = ['tashkeel', 'transliteration', 'numbers', 'text-cleaner'];
foreach ($services as $service) {
    if (Arabic::container()->has($service)) {
        $svc = Arabic::container()->get($service);
        echo "  ✓ {$service} - " . ($svc->isAvailable() ? 'Ready' : 'Unavailable') . "\n";
    }
}

echo "\n🎯 All Arabic text processing features working perfectly!\n";
echo "   You can now process Arabic text without any AI dependencies.\n\n";
