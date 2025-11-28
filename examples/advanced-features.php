<?php

require_once __DIR__ . '/../vendor/autoload.php';

use ArPHP\Core\Arabic;
use ArPHP\Core\Modules\{
    StemmingModule,
    SentimentModule,
    KeyboardModule,
    StatisticsModule,
    TashkeelModule,
    TextCleanerModule
};

/**
 * Advanced Arabic Text Processing Demo
 * 
 * Demonstrates new advanced features
 */

echo str_repeat('=', 70) . "\n";
echo "🚀 Advanced Arabic Text Processing\n";
echo str_repeat('=', 70) . "\n\n";

// Initialize with all modules
Arabic::init([
    new StemmingModule(),
    new SentimentModule(),
    new KeyboardModule(),
    new StatisticsModule(),
    new TashkeelModule(),
    new TextCleanerModule(),
]);

echo "✅ All advanced modules loaded!\n\n";

// ============================================
// 1. Stemming (Root Extraction)
// ============================================

echo str_repeat('=', 70) . "\n";
echo "1️⃣ Stemming - Root Extraction\n";
echo str_repeat('-', 70) . "\n\n";

$stemmer = Arabic::container()->get('stemming');

$words = ['مكتبة', 'المكتبات', 'كتاب', 'كتب', 'يكتبون', 'المدرسة', 'المدرسون', 'درس'];

echo "Extract roots:\n";
foreach ($words as $word) {
    echo "  {$word} → " . $stemmer->stem($word) . "\n";
}

echo "\nExtract unique roots from text:\n";
$text = 'المكتبة تحتوي على كتب كثيرة والطلاب يكتبون في المدرسة';
$roots = $stemmer->extractRoots($text);
echo "Text: {$text}\n";
echo "Roots: " . implode(', ', $roots) . "\n\n";

// ============================================
// 2. Sentiment Analysis
// ============================================

echo str_repeat('=', 70) . "\n";
echo "2️⃣ Sentiment Analysis (Dictionary-based)\n";
echo str_repeat('-', 70) . "\n\n";

$sentiment = Arabic::container()->get('sentiment');

$reviews = [
    'هذا المنتج رائع جداً وأنصح الجميع بتجربته!',
    'خدمة سيئة للغاية ولا أنصح بها',
    'المنتج جيد لكن السعر مرتفع',
    'ممتاز وسريع ومفيد جداً',
    'فظيع ومخيب للآمال',
];

echo "Analyze customer reviews:\n\n";
foreach ($reviews as $i => $review) {
    $result = $sentiment->analyze($review);
    $emoji = match ($result['sentiment']) {
        'positive' => '😊',
        'negative' => '😞',
        default => '😐',
    };
    
    echo ($i + 1) . ". \"{$review}\"\n";
    echo "   {$emoji} Sentiment: {$result['sentiment']} ";
    echo "(confidence: {$result['confidence']}, score: {$result['score']})\n";
    echo "   Positive: {$result['positive']}%, Negative: {$result['negative']}%\n\n";
}

// ============================================
// 3. Keyboard Correction
// ============================================

echo str_repeat('=', 70) . "\n";
echo "3️⃣ Keyboard Layout Correction\n";
echo str_repeat('-', 70) . "\n\n";

$keyboard = Arabic::container()->get('keyboard');

echo "Fix English typed as Arabic:\n";
$typos = [
    'lhv hggi' => 'بسم الله',
    'hgsghl ugd;l' => 'السلام عليكم',
    'lv,fh' => 'مرحبا',
];

foreach ($typos as $wrong => $expected) {
    $fixed = $keyboard->fixEnglishTypedAsArabic($wrong);
    $match = $fixed === $expected ? '✓' : '✗';
    echo "  {$match} \"{$wrong}\" → \"{$fixed}\"\n";
}

echo "\nFix Arabic typed as English:\n";
$arabicTypos = [
    'يثممخ' => 'hello',
    'صخقمي' => 'world',
];

foreach ($arabicTypos as $wrong => $expected) {
    $fixed = $keyboard->fixArabicTypedAsEnglish($wrong);
    echo "  \"{$wrong}\" → \"{$fixed}\" (expected: {$expected})\n";
}

echo "\nAuto-detect and fix:\n";
$autoFix = [
    'lhv hggi',
    'مرحبا',
    'صخقمي',
];

foreach ($autoFix as $text) {
    $suggestion = $keyboard->getSuggestion($text);
    if ($suggestion['was_fixed']) {
        echo "  ⚠️  \"{$suggestion['original']}\" → \"{$suggestion['fixed']}\"\n";
    } else {
        echo "  ✓  \"{$text}\" (already correct)\n";
    }
}

echo "\n";

// ============================================
// 4. Text Statistics
// ============================================

echo str_repeat('=', 70) . "\n";
echo "4️⃣ Text Statistics\n";
echo str_repeat('-', 70) . "\n\n";

$stats = Arabic::container()->get('statistics');

$sampleText = <<<TEXT
اللغة العربية هي إحدى أكثر اللغات انتشاراً في العالم.
يتحدثها أكثر من 400 مليون نسمة.
تعتبر اللغة العربية من اللغات السامية.
لها تاريخ عريق وثقافة غنية.
TEXT;

echo "Sample text:\n{$sampleText}\n\n";

$analysis = $stats->analyze($sampleText);

echo "📊 Analysis Results:\n";
echo "  Characters: {$analysis['characters']}\n";
echo "  Words: {$analysis['words']}\n";
echo "  Sentences: {$analysis['sentences']}\n";
echo "  Paragraphs: {$analysis['paragraphs']}\n";
echo "  Unique words: {$analysis['unique_words']}\n\n";

echo "📈 Averages:\n";
echo "  Word length: {$analysis['averages']['word_length']} chars\n";
echo "  Words per sentence: {$analysis['averages']['words_per_sentence']}\n";
echo "  Sentences per paragraph: {$analysis['averages']['sentences_per_paragraph']}\n\n";

echo "📚 Most common words:\n";
foreach ($analysis['word_frequency'] as $word => $count) {
    echo "  {$word}: {$count}x\n";
}

echo "\n📖 Readability: {$analysis['readability']}/10\n\n";

$summary = $stats->getSummary($sampleText);
echo "Summary:\n";
echo "  Length: {$summary['length']}\n";
echo "  Complexity: {$summary['complexity']}\n";
echo "  Diversity: {$summary['diversity']}\n\n";

// ============================================
// 5. Batch Processing Demo
// ============================================

echo str_repeat('=', 70) . "\n";
echo "5️⃣ Batch Processing\n";
echo str_repeat('-', 70) . "\n\n";

$texts = [
    'مَرْحَبًا بِكُم',
    'السَّلامُ عَلَيْكُم',
    'كَيْفَ حالُكُم',
];

echo "Batch remove tashkeel:\n";
$tashkeel = Arabic::container()->get('tashkeel');
$cleaned = $tashkeel->removeBatch($texts);
foreach ($texts as $i => $text) {
    echo "  {$text} → {$cleaned[$i]}\n";
}

echo "\nBatch sentiment analysis:\n";
$reviews = [
    'منتج رائع',
    'سيئ جداً',
    'جيد',
];
$sentiments = $sentiment->analyzeBatch($reviews);
foreach ($reviews as $i => $review) {
    echo "  \"{$review}\" → {$sentiments[$i]['sentiment']}\n";
}

echo "\nBatch root extraction:\n";
$words = ['كتاب', 'مكتبة', 'يكتبون'];
$roots = $stemmer->stemBatch($words);
foreach ($words as $i => $word) {
    echo "  {$word} → {$roots[$i]}\n";
}

echo "\n";

// ============================================
// 6. Combined Real-World Example
// ============================================

echo str_repeat('=', 70) . "\n";
echo "6️⃣ Real-World Example: Review Processing Pipeline\n";
echo str_repeat('-', 70) . "\n\n";

$userReview = "lhv hggi! ` هذا المنتج رائع جداً وأنصح به الجميع. المكتبة مذهلة!";

echo "Original review:\n\"{$userReview}\"\n\n";

echo "Processing pipeline:\n";

// Step 1: Fix keyboard layout
$fixed = $keyboard->autoFix($userReview);
echo "1. Fix keyboard: {$fixed}\n";

// Step 2: Clean text
$cleaner = Arabic::container()->get('text-cleaner');
$cleaned = $cleaner->clean($fixed);
echo "2. Clean text: {$cleaned}\n";

// Step 3: Analyze sentiment
$sentimentResult = $sentiment->analyze($cleaned);
echo "3. Sentiment: {$sentimentResult['sentiment']} (confidence: {$sentimentResult['confidence']})\n";

// Step 4: Get statistics
$statsResult = $stats->analyze($cleaned);
echo "4. Word count: {$statsResult['words']}, Readability: {$statsResult['readability']}/10\n";

// Step 5: Extract roots
$roots = $stemmer->extractRoots($cleaned);
echo "5. Unique roots: " . implode(', ', array_slice($roots, 0, 5)) . "...\n";

echo "\n✅ Processing complete!\n\n";

// ============================================
// Summary
// ============================================

echo str_repeat('=', 70) . "\n";
echo "📊 Summary\n";
echo str_repeat('=', 70) . "\n\n";

echo "Advanced modules loaded:\n";
foreach (Arabic::registry()->all() as $module) {
    echo "  ✓ " . $module->getName() . " v" . $module->getVersion() . "\n";
}

echo "\n🎯 All advanced features working perfectly!\n";
echo "   Package now includes:\n";
echo "   • Stemming (root extraction)\n";
echo "   • Sentiment analysis (dictionary-based)\n";
echo "   • Keyboard correction\n";
echo "   • Text statistics\n";
echo "   • Batch processing support\n\n";
