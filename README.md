# ArPHP - Arabic NLP Library for PHP 8.4+

<div align="center">

![PHP Version](https://img.shields.io/badge/PHP-8.4%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![License](https://img.shields.io/badge/License-MIT-green?style=for-the-badge)
![Arabic NLP](https://img.shields.io/badge/Arabic-NLP-blue?style=for-the-badge)

**مكتبة PHP شاملة لمعالجة اللغة العربية**

[English](#features) | [العربية](#المميزات-بالعربية)

</div>

---

## 📦 Installation | التثبيت

```bash
composer require waleedelsefy/ar-php-core
```

### Requirements | المتطلبات

- PHP 8.4 or higher
- ext-mbstring
- ext-json

---

## 🚀 Quick Start | البداية السريعة

```php
<?php

require_once 'vendor/autoload.php';

use ArPHP\Core\Modules\Normalizer\Normalizer;
use ArPHP\Core\Modules\Tokenizer\Tokenizer;
use ArPHP\Core\Modules\Sentiment\Sentiment;

// Normalize Arabic text
$text = Normalizer::normalize('أحمد وإبراهيم');
// Output: احمد وابراهيم

// Tokenize text
$tokens = Tokenizer::tokenize('مرحباً بالعالم');
// Output: ['مرحباً', 'بالعالم']

// Analyze sentiment
$result = Sentiment::analyze('هذا المنتج رائع وممتاز');
// Output: ['sentiment' => 'positive', 'score' => 0.85]
```

---

## ✨ Features

ArPHP provides **20 powerful modules** for Arabic text processing:

### 📅 Date & Time | التاريخ والوقت
| Module | Facade | Description |
|--------|--------|-------------|
| **Hijri Calendar** | `Hijri::` | Hijri↔Gregorian date conversion |
| **Prayer Times** | `Prayer::` | Islamic prayer times calculation |

### 📝 Text Processing | معالجة النصوص
| Module | Facade | Description |
|--------|--------|-------------|
| **Normalizer** | `Normalizer::` | Arabic text normalization (Alef, Yeh, Teh Marbuta) |
| **Tokenizer** | `Tokenizer::` | Text tokenization with Arabic support |
| **Tashkeel** | `Tashkeel::` | Diacritics (harakat) handling |
| **Stopwords** | `Stopwords::` | Arabic stopword filtering |
| **Slugify** | `Slugify::` | Generate URL-safe Arabic slugs |

### 🔤 Transliteration | التحويل الصوتي
| Module | Facade | Description |
|--------|--------|-------------|
| **Buckwalter** | `Buckwalter::` | Buckwalter transliteration system |
| **Soundex** | `Soundex::` | Arabic phonetic matching algorithm |
| **Keyboard Fix** | `Keyboard::` | Fix Arabic/English keyboard mistakes |

### 🧠 NLP & Analysis | الذكاء الاصطناعي
| Module | Facade | Description |
|--------|--------|-------------|
| **Sentiment** | `Sentiment::` | Sentiment analysis (positive/negative/neutral) |
| **NER** | `NER::` | Named Entity Recognition (Person, Location, Organization) |
| **Lemmatizer** | `Lemmatizer::` | Arabic root/lemma extraction |
| **Summarizer** | `Summarizer::` | Extractive text summarization |
| **Word Frequency** | `WordFrequency::` | Word frequency & TF-IDF analysis |
| **Spell Checker** | `SpellChecker::` | Spelling validation & suggestions |

### 🌍 Regional | إقليمي
| Module | Facade | Description |
|--------|--------|-------------|
| **Dialect Normalizer** | `Dialect::` | Normalize dialects to Modern Standard Arabic |
| **Name Parser** | `NameParser::` | Parse Arabic names (first, father, family) |

### 🔧 Utilities | أدوات
| Module | Facade | Description |
|--------|--------|-------------|
| **Encoding** | `Encoding::` | Character encoding conversion |
| **Quran Search** | `Quran::` | Search within Quran text |

---

## 📚 Module Usage Examples

### Hijri Calendar | التقويم الهجري

```php
use ArPHP\Core\Modules\HijriCalendar\Hijri;

// Convert Gregorian to Hijri
$hijri = Hijri::fromGregorian(2025, 11, 28);
// ['year' => 1447, 'month' => 5, 'day' => 6]

// Convert Hijri to Gregorian
$gregorian = Hijri::toGregorian(1447, 5, 6);

// Get current Hijri date
$today = Hijri::today();

// Format Hijri date in Arabic
echo Hijri::format($hijri, 'ar');
// Output: ٦ جمادى الأولى ١٤٤٧
```

### Prayer Times | أوقات الصلاة

```php
use ArPHP\Core\Modules\PrayerTimes\Prayer;

// Get prayer times for Cairo
$times = Prayer::calculate(30.0444, 31.2357, '2025-11-28');
// [
//     'fajr' => '05:12',
//     'sunrise' => '06:35',
//     'dhuhr' => '11:52',
//     'asr' => '14:42',
//     'maghrib' => '17:09',
//     'isha' => '18:30'
// ]

// Get next prayer
$next = Prayer::nextPrayer(30.0444, 31.2357);
```

### Text Normalization | تطبيع النص

```php
use ArPHP\Core\Modules\Normalizer\Normalizer;

// Full normalization
$text = Normalizer::normalize('أَحْمَدُ وَإِبْرَاهِيمُ');
// Output: احمد وابراهيم

// Normalize only Alef
$text = Normalizer::normalizeAlef('أإآٱ');
// Output: ااااا

// Remove diacritics
$text = Normalizer::removeDiacritics('مُحَمَّد');
// Output: محمد

// Normalize Teh Marbuta
$text = Normalizer::normalizeTehMarbuta('مدرسة');
// Output: مدرسه
```

### Tashkeel (Diacritics) | التشكيل

```php
use ArPHP\Core\Modules\Tashkeel\Tashkeel;

// Remove all diacritics
$clean = Tashkeel::strip('مُحَمَّدٌ رَسُولُ اللهِ');
// Output: محمد رسول الله

// Check if text has diacritics
$hasTashkeel = Tashkeel::has('مُحَمَّد'); // true

// Count diacritics
$count = Tashkeel::count('مُحَمَّدٌ'); // 4

// Get diacritic statistics
$stats = Tashkeel::stats('مُحَمَّدٌ');
// ['fatha' => 1, 'damma' => 1, 'shadda' => 1, 'dammatan' => 1]

// Get tashkeel density
$density = Tashkeel::density($text); // 0.75
```

### Sentiment Analysis | تحليل المشاعر

```php
use ArPHP\Core\Modules\Sentiment\Sentiment;

// Analyze sentiment
$result = Sentiment::analyze('هذا الفيلم رائع ومميز جداً');
// [
//     'sentiment' => 'positive',
//     'score' => 0.85,
//     'positive_words' => ['رائع', 'مميز'],
//     'negative_words' => []
// ]

// Quick classification
$sentiment = Sentiment::classify('الخدمة سيئة للغاية');
// Output: 'negative'

// Check sentiment type
Sentiment::isPositive('منتج ممتاز'); // true
Sentiment::isNegative('تجربة سيئة'); // true
```

### Named Entity Recognition | التعرف على الكيانات

```php
use ArPHP\Core\Modules\NER\NER;

$text = 'زار الرئيس محمد القاهرة يوم الخميس';

// Extract all entities
$entities = NER::extract($text);
// [
//     ['entity' => 'محمد', 'type' => 'PERSON'],
//     ['entity' => 'القاهرة', 'type' => 'LOCATION']
// ]

// Extract specific entity types
$persons = NER::extractPersons($text);
$locations = NER::extractLocations($text);
$organizations = NER::extractOrganizations($text);
```

### Text Summarization | تلخيص النصوص

```php
use ArPHP\Core\Modules\Summarizer\Summarizer;

$article = 'نص طويل يحتوي على عدة فقرات...';

// Summarize to 3 sentences
$summary = Summarizer::summarize($article, 3);

// Summarize by ratio (30% of original)
$summary = Summarizer::byRatio($article, 0.3);

// Generate headline
$headline = Summarizer::headline($article, 100);

// Extract keywords
$keywords = Summarizer::keywords($article, 10);

// Get text statistics
$stats = Summarizer::stats($article);
// ['total_words' => 500, 'total_sentences' => 25, ...]
```

### Dialect Normalization | تطبيع اللهجات

```php
use ArPHP\Core\Modules\DialectNormalizer\Dialect;

// Normalize Egyptian dialect
$msa = Dialect::egyptian('ازيك عامل ايه');
// Output: كيف حالك ماذا تفعل

// Normalize Gulf dialect
$msa = Dialect::gulf('شلونك وينك');
// Output: كيف حالك أين أنت

// Normalize Levantine dialect
$msa = Dialect::levantine('كيفك وين رايح');
// Output: كيف حالك أين ذاهب

// Auto-detect and normalize
$msa = Dialect::normalize($text);
```

### Slugify | إنشاء الروابط

```php
use ArPHP\Core\Modules\Slugify\Slugify;

// Generate URL-safe slug
$slug = Slugify::make('مقال عن البرمجة بلغة PHP');
// Output: mqal-an-albrmjh-blghh-php

// Custom separator
$slug = Slugify::make('مرحباً بالعالم', '_');
// Output: mrhba_balalm
```

### Buckwalter Transliteration | نظام باكوالتر

```php
use ArPHP\Core\Modules\BuckwalterTransliteration\Buckwalter;

// Arabic to Buckwalter
$buckwalter = Buckwalter::encode('محمد');
// Output: mHmd

// Buckwalter to Arabic
$arabic = Buckwalter::decode('mHmd');
// Output: محمد
```

### Tokenizer | تقطيع النص

```php
use ArPHP\Core\Modules\Tokenizer\Tokenizer;

// Tokenize text
$tokens = Tokenizer::tokenize('مرحباً بالعالم العربي');
// ['مرحباً', 'بالعالم', 'العربي']

// Tokenize with punctuation
$tokens = Tokenizer::tokenizeWithPunctuation('مرحباً! كيف حالك؟');

// Get sentences
$sentences = Tokenizer::sentences($text);

// Get word count
$count = Tokenizer::wordCount($text);
```

### Word Frequency | تردد الكلمات

```php
use ArPHP\Core\Modules\WordFrequency\WordFrequency;

// Analyze word frequency
$freq = WordFrequency::analyze($text);
// ['الكلمة' => 5, 'النص' => 3, ...]

// Get top N words
$top = WordFrequency::topWords($text, 10);

// Calculate TF-IDF
$tfidf = WordFrequency::tfidf($text, $corpus);
```

### Spell Checker | التدقيق الإملائي

```php
use ArPHP\Core\Modules\SpellChecker\SpellChecker;

// Check spelling
$isCorrect = SpellChecker::check('محمد'); // true

// Get suggestions
$suggestions = SpellChecker::suggest('محمود');
// ['محمد', 'محمود', 'حمود']

// Check and correct text
$corrected = SpellChecker::correct($text);
```

### Keyboard Layout Fix | تصحيح لوحة المفاتيح

```php
use ArPHP\Core\Modules\AdvancedKeyboardFix\Keyboard;

// Fix Arabic typed with English layout
$fixed = Keyboard::fixArabic('lphf');
// Output: مرحب

// Fix English typed with Arabic layout
$fixed = Keyboard::fixEnglish('اثممخ');
// Output: hello
```

### Lemmatizer | استخراج الجذور

```php
use ArPHP\Core\Modules\Lemmatizer\Lemmatizer;

// Get word root
$root = Lemmatizer::root('يكتبون');
// Output: كتب

// Get lemma
$lemma = Lemmatizer::lemmatize('المدرسة');
// Output: درس

// Analyze morphology
$analysis = Lemmatizer::analyze('يكتبون');
// ['root' => 'كتب', 'pattern' => 'يفعلون', 'prefix' => 'ي', 'suffix' => 'ون']
```

---

## 🏗️ Architecture

```
packages/core/src/
├── AbstractModule.php          # Base module class
├── Arabic.php                  # Main entry point
├── ModuleRegistry.php          # Module registration
├── ServiceContainer.php        # DI container
├── Contracts/                  # Core interfaces
├── Exceptions/                 # Core exceptions
└── Modules/
    ├── Normalizer/
    │   ├── Contracts/
    │   │   └── NormalizerInterface.php
    │   ├── Exceptions/
    │   │   └── NormalizerException.php
    │   ├── Services/
    │   │   └── NormalizerService.php
    │   ├── Config.php
    │   ├── Normalizer.php      # Static Facade
    │   └── NormalizerModule.php
    ├── Tokenizer/
    ├── Sentiment/
    └── ... (20 modules)
```

### Module Structure | هيكل الوحدات

كل وحدة تتبع نفس الهيكل:

```
ModuleName/
├── Contracts/
│   └── ModuleNameInterface.php    # واجهة الوحدة
├── Exceptions/
│   └── ModuleNameException.php    # استثناءات مخصصة
├── Services/
│   └── ModuleNameService.php      # منطق الأعمال
├── Config.php                     # إعدادات ثابتة
├── ModuleName.php                 # Facade ثابت
└── ModuleNameModule.php           # الوحدة الرئيسية
```

---

## 🔧 Advanced Usage

### Using with Dependency Injection

```php
use ArPHP\Core\Modules\Normalizer\NormalizerModule;
use ArPHP\Core\Modules\Sentiment\SentimentModule;

class TextProcessor
{
    public function __construct(
        private NormalizerModule $normalizer,
        private SentimentModule $sentiment
    ) {}

    public function process(string $text): array
    {
        $normalized = $this->normalizer->normalize($text);
        $sentiment = $this->sentiment->analyze($normalized);
        
        return [
            'normalized' => $normalized,
            'sentiment' => $sentiment
        ];
    }
}
```

### Chaining Operations

```php
use ArPHP\Core\Modules\Normalizer\Normalizer;
use ArPHP\Core\Modules\Stopwords\Stopwords;
use ArPHP\Core\Modules\Tokenizer\Tokenizer;

$text = 'هذا النص العربي يحتاج إلى معالجة';

// Process pipeline
$tokens = Tokenizer::tokenize(
    Stopwords::filter(
        Normalizer::normalize($text)
    )
);
```

### Laravel Integration | التكامل مع Laravel

```php
// config/services.php
return [
    'arphp' => [
        'normalizer' => \ArPHP\Core\Modules\Normalizer\NormalizerModule::class,
        'sentiment' => \ArPHP\Core\Modules\Sentiment\SentimentModule::class,
    ],
];

// AppServiceProvider.php
public function register(): void
{
    $this->app->singleton(NormalizerModule::class);
    $this->app->singleton(SentimentModule::class);
}
```

---

## 🧪 Testing | الاختبارات

```bash
# Run all tests
./vendor/bin/phpunit

# Run specific module tests
./vendor/bin/phpunit --filter NormalizerTest

# Run with coverage
./vendor/bin/phpunit --coverage-html coverage/
```

---

## 📊 PHP 8.4 Features Used

هذه المكتبة تستخدم أحدث مميزات PHP 8.4:

| Feature | Usage |
|---------|-------|
| `readonly class` | Config classes |
| Typed class constants | `public const string NAME = 'value'` |
| Constructor property promotion | Dependency injection |
| Named arguments | Method calls |
| Enums | Status types |
| Match expressions | Control flow |
| Nullsafe operator | Optional chaining |
| Union types | Parameter flexibility |

---

## المميزات بالعربية

مكتبة ArPHP توفر **20 وحدة متكاملة** لمعالجة اللغة العربية:

### ✅ الوحدات المتوفرة

| # | الوحدة | الوصف |
|---|--------|-------|
| 1 | **التقويم الهجري** | تحويل التواريخ بين الهجري والميلادي |
| 2 | **أوقات الصلاة** | حساب مواقيت الصلاة لأي موقع جغرافي |
| 3 | **الساوندكس العربي** | المطابقة الصوتية للكلمات |
| 4 | **تحليل الأسماء** | تقسيم الأسماء العربية |
| 5 | **الترميز** | تحويل بين ترميزات النصوص |
| 6 | **البحث القرآني** | البحث في نصوص القرآن |
| 7 | **تصحيح لوحة المفاتيح** | إصلاح أخطاء الكتابة |
| 8 | **نظام باكوالتر** | التحويل الصوتي |
| 9 | **تقطيع النص** | تجزئة النصوص لكلمات |
| 10 | **التطبيع** | توحيد الأحرف العربية |
| 11 | **كلمات الوقف** | تصفية الكلمات الشائعة |
| 12 | **استخراج الجذور** | التحليل الصرفي |
| 13 | **التدقيق الإملائي** | فحص واقتراح التصحيحات |
| 14 | **تحليل المشاعر** | تصنيف النصوص عاطفياً |
| 15 | **التعرف على الكيانات** | استخراج الأسماء والأماكن |
| 16 | **تطبيع اللهجات** | تحويل اللهجات للفصحى |
| 17 | **إنشاء الروابط** | تحويل النص لروابط آمنة |
| 18 | **تردد الكلمات** | تحليل تكرار الكلمات |
| 19 | **التشكيل** | إدارة الحركات |
| 20 | **التلخيص** | تلخيص النصوص الطويلة |

### 💡 مميزات تقنية

- ✅ دعم كامل لـ PHP 8.4+
- ✅ كلاسات readonly للأداء
- ✅ ثوابت بأنواع محددة
- ✅ واجهات ثابتة (Static Facades) سهلة الاستخدام
- ✅ دعم حقن التبعيات (DI)
- ✅ متوافق مع PSR-4
- ✅ اختبارات شاملة
- ✅ توثيق كامل

---

## 🤝 Contributing | المساهمة

المساهمات مرحب بها! اقرأ [CONTRIBUTING.md](CONTRIBUTING.md) للتفاصيل.

```bash
# Clone the repository
git clone https://github.com/waleedelsefy/ar-php.git

# Install dependencies
composer install

# Run tests
./vendor/bin/phpunit

# Run static analysis
./vendor/bin/phpstan analyse
```

---

## 📄 License | الرخصة

هذا المشروع مرخص تحت رخصة MIT - راجع ملف [LICENSE](LICENSE) للتفاصيل.

---

## 🙏 Credits | الإسناد

This project is built upon and inspired by the original [ArPHP](https://github.com/khaled-alshamaa/ar-php) library by **Khaled Al-Sham'aa**.

هذا المشروع مبني على مكتبة [ArPHP](https://github.com/khaled-alshamaa/ar-php) الأصلية للمطور **خالد الشمعة**.

> **Original Library**: [github.com/khaled-alshamaa/ar-php](https://github.com/khaled-alshamaa/ar-php)
> 
> شكر خاص للمجهود الكبير في المكتبة الأصلية التي كانت الأساس لهذا المشروع المُحدّث.

---

## 👨‍💻 Author | المؤلف

**Waleed Elsefy**

- GitHub: [@waleedelsefy](https://github.com/waleedelsefy)

---

## 🌟 Support | الدعم

إذا وجدت هذه المكتبة مفيدة، يرجى إعطاؤها نجمة ⭐ على GitHub!

---

<div align="center">

**Made with ❤️ for the Arabic-speaking developer community**

**مصنوع بـ ❤️ لمجتمع المطورين العرب**

</div>
