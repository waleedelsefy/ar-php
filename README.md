# ArPHP Enhanced - Modular Arabic Text Processing

Modern, high-performance, truly modular Arabic text processing library for PHP 8.1+

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-8892BF.svg)](https://php.net/)

## Features

### Core Features
- **🎯 100% Modular** - Load ONLY what you need (Core is just 12KB!)
- **🔌 PSR-11 Container** - Standard dependency injection
- **⚡ Zero Dependencies** - Core needs only PHP 8.1+ and PSR-11
- **🔒 Type Safe** - Full type hints and strict types
- **🧩 Plugin Ready** - Easy to extend with custom modules

### Arabic Text Processing (No AI Required!)
- **✨ Tashkeel** - Add/remove Arabic diacritics (التشكيل)
- **🔤 Transliteration** - Arabic ↔ Latin (ALA-LC, Buckwalter, ISO-233)
- **🔢 Numbers** - Arabic-Indic ↔ Western numerals, numbers to words
- **🧹 Text Cleaning** - Remove HTML, URLs, normalize text
- **🌱 Stemming** - Extract Arabic roots for search & analysis
- **😊 Sentiment** - Dictionary-based sentiment analysis
- **⌨️ Keyboard** - Fix Arabic/English keyboard layout mistakes
- **📊 Statistics** - Word count, readability, text analysis

### AI Integration (Optional)
- **🤖 Multi-AI Support** - 17 models from Gemini, OpenAI, Claude
- **🆓 Free Models** - Llama, Mistral, Cohere, DeepSeek
- **🎯 Model Selection** - Choose specific models for your needs

## Requirements

- PHP 8.1 or higher
- ext-mbstring extension
- Composer

## Installation

```bash
composer require waleedelsefy/ar-php
```

## Quick Start

### 1️⃣ Arabic Text Processing (No AI Required!)
```php
use ArPHP\Core\Arabic;
use ArPHP\Core\Modules\{TashkeelModule, TransliterationModule, NumbersModule, TextCleanerModule};

// Load Arabic processing modules
Arabic::init([
    new TashkeelModule(),         // Diacritics (التشكيل)
    new TransliterationModule(),  // Arabic-Latin conversion
    new NumbersModule(),          // Number conversion
    new TextCleanerModule(),      // Text cleaning
]);

// Use them!
$tashkeel = Arabic::container()->get('tashkeel');
echo $tashkeel->remove('مَرْحَبًا'); // مرحبا

$numbers = Arabic::container()->get('numbers');
echo $numbers->toArabicIndic('123'); // ١٢٣
```

### 2️⃣ Minimal Core (Framework Only)
```php
use ArPHP\Core\Arabic;

// Just the core framework - 12KB!
Arabic::init();  // That's it!
```

### 3️⃣ With AI (Load what you need)
```php
// Option A: Premium models
Arabic::init([
    new ConfigurableAIModule('openai', 'chatgpt-5.1'),
    new ConfigurableAIModule('claude', 'claude-sonnet-4.5'),
]);

// Option B: Free models (no cost!)
Arabic::init([
    new ConfigurableAIModule('mistral', 'mistral-7b'),      // FREE
    new ConfigurableAIModule('cohere', 'command-r'),        // FREE
]);

// Use them
$ai = Arabic::container()->get('mistral-mistral-7b');
```

### 4️⃣ Configuration
```bash
# Copy template
cp .env.example .env

# Add your API keys (optional for free models)
GEMINI_API_KEY=your_key_here
OPENAI_API_KEY=your_key_here
CLAUDE_API_KEY=your_key_here

# Free models don't need keys!
# Just use: mistral-7b, llama-3.1-8b, command-r, deepseek-chat
```

See [QUICKSTART.md](QUICKSTART.md) for detailed guide.

## Arabic Text Processing Features

### ✨ Tashkeel (التشكيل)
```php
$tashkeel = Arabic::container()->get('tashkeel');

// Remove diacritics
$tashkeel->remove('بِسْمِ اللَّهِ');  // بسم الله

// Add diacritics (common words)
$tashkeel->add('مرحبا');  // مَرْحَبًا

// Normalize text
$tashkeel->normalize('أحمد');  // احمد
```

### 🔤 Transliteration  
```php
$trans = Arabic::container()->get('transliteration');

// Arabic → Latin
$trans->toLatin('محمد');  // mhmd

// Latin → Arabic  
$trans->toArabic('ahmad');  // احماد

// Auto-detect
$trans->convert('خالد');  // khalid
```

### 🔢 Numbers
```php
$numbers = Arabic::container()->get('numbers');

// Western → Arabic-Indic
$numbers->toArabicIndic('123');  // ١٢٣

// Arabic-Indic → Western
$numbers->toWestern('٤٥٦');  // 456

// Numbers → Words
$numbers->toWords(25);  // خمسة وعشرون

// Format with Arabic separators
$numbers->format(1234567.89, 2);  // ١٬٢٣٤٬٥٦٧٫٨٩
```

### 🧹 Text Cleaning
```php
$cleaner = Arabic::container()->get('text-cleaner');

// Remove extra spaces
$cleaner->removeExtraSpaces('  نص   مع   مسافات  ');

// Remove HTML tags
$cleaner->removeHtml('<p>نص عربي</p>');

// Remove URLs, emails, numbers, etc.
$cleaner->clean($text, [
    'html' => true,
    'urls' => true,
    'english' => true,
    'numbers' => false,
]);

// Count words and characters
$cleaner->countWords('هذا نص عربي');  // 3
$cleaner->countChars('هذا');  // 3
```

### 🌱 Stemming (Root Extraction)
```php
$stemmer = Arabic::container()->get('stemming');

// Extract root
$stemmer->stem('مكتبة');  // مكتب
$stemmer->stem('يكتبون');  // كتب

// Extract all roots from text
$roots = $stemmer->extractRoots('المكتبة تحتوي على كتب');
// ['مكتب', 'تحت', 'كتب', ...]

// Batch processing
$roots = $stemmer->stemBatch(['كتاب', 'مكتبة', 'كاتب']);
```

### 😊 Sentiment Analysis
```php
$sentiment = Arabic::container()->get('sentiment');

// Analyze sentiment
$result = $sentiment->analyze('هذا المنتج رائع جداً!');
// [
//   'sentiment' => 'positive',
//   'confidence' => 0.95,
//   'score' => 1.5,
// ]

// Batch analysis
$results = $sentiment->analyzeBatch($reviews);
```

### ⌨️ Keyboard Correction
```php
$keyboard = Arabic::container()->get('keyboard');

// Fix English typed as Arabic
$keyboard->fixEnglishTypedAsArabic('lhv hggi');
// بسم الله

// Fix Arabic typed as English
$keyboard->fixArabicTypedAsEnglish('صخقمي');
// world

// Auto-detect and fix
$keyboard->autoFix($text);
```

### 📊 Text Statistics
```php
$stats = Arabic::container()->get('statistics');

$analysis = $stats->analyze($text);
// [
//   'characters' => 150,
//   'words' => 35,
//   'sentences' => 4,
//   'readability' => 6.2,
//   'unique_words' => 28,
//   'word_frequency' => [...],
// ]
```

### ⚡ Batch Processing
All modules support batch processing for better performance:
```php
// Process 1000s of texts efficiently
$cleaned = $tashkeel->removeBatch($texts);
$sentiments = $sentiment->analyzeBatch($reviews);
$roots = $stemmer->stemBatch($words);
$transliterated = $trans->toLatinBatch($texts, 'buckwalter');
```

## Creating Custom Modules

```php
<?php

use ArPHP\Core\Arabic;
use ArPHP\Core\AbstractModule;

class MyCustomModule extends AbstractModule
{
    public function getName(): string
    {
        return 'my-module';
    }
    
    public function register(): void
    {
        Arabic::container()->register('my-service', fn() => new MyService());
    }
    
    public function boot(): void
    {
        // Module is ready
    }
}

// Initialize with custom module
Arabic::init([new MyCustomModule()]);
```

## AI Integration

ArPHP Enhanced supports multiple AI providers simultaneously:

```php
// Use multiple AI providers together
Arabic::init([
    new GeminiModule(),
    new OpenAIModule(),
    new ClaudeModule(),
    new AIAggregatorModule(),  // Combines all
]);

// Get consensus from all providers
$aggregator = Arabic::container()->get('ai-aggregator');
$results = $aggregator->analyzeWithAll('النص العربي');
$consensus = $aggregator->getConsensus($results);
```

**Supported Providers & Models:**
- 🔷 **Google Gemini**: `gemini-pro`, `gemini-3-pro`, `gemini-pro-vision`
- 🔶 **OpenAI**: `gpt-3.5-turbo`, `gpt-4`, `gpt-4-turbo`, `chatgpt-5`, `chatgpt-5.1`
- 🔵 **Anthropic Claude**: `claude-3-haiku`, `claude-3-sonnet`, `claude-3-opus`, `claude-sonnet-4`, `claude-sonnet-4.5`
- 🆓 **Free Models**: `llama-3.1-8b`, `mistral-7b`, `command-r`, `deepseek-chat`

**Select Specific Models:**
```php
// Register specific models
Arabic::init([
    new ConfigurableAIModule('openai', 'chatgpt-5.1'),
    new ConfigurableAIModule('claude', 'claude-sonnet-4.5'),
    new ConfigurableAIModule('gemini', 'gemini-3-pro'),
]);

// Use latest models
$chatgpt = Arabic::container()->get('openai-chatgpt-5.1');
$claude = Arabic::container()->get('claude-claude-sonnet-4.5');
$gemini = Arabic::container()->get('gemini-gemini-3-pro');
```

See [AI Integration](docs/AI_INTEGRATION.md), [Multi-AI Providers](docs/MULTI_AI_PROVIDERS.md), and [Model Selection](docs/MODEL_SELECTION.md) for complete guides.

## Documentation

See the `/docs` folder for complete documentation:

- [Getting Started](docs/GETTING_STARTED.md)
- [Architecture](docs/ARCHITECTURE.md)
- [API Reference](docs/API_REFERENCE.md)
- [AI Integration](docs/AI_INTEGRATION.md) 🤖

## Testing

```bash
composer test
```

## Static Analysis

```bash
composer phpstan
```

## License

MIT License - see [LICENSE](LICENSE) file for details.

## Author

Waleed Elsefy
