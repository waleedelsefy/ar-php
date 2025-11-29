<p align="center">
  <img src="website/public/logo.png" alt="عربي PHP" width="120">
</p>

<h1 align="center">عربي PHP</h1>
<h3 align="center">Arabic PHP - مكتبة معالجة اللغة العربية</h3>

<p align="center">
  <a href="https://packagist.org/packages/waleedelsefy/ar-php">
    <img src="https://img.shields.io/packagist/v/waleedelsefy/ar-php?style=flat-square&color=9A1F2C" alt="Packagist Version">
  </a>
  <a href="https://php.net">
    <img src="https://img.shields.io/badge/PHP-8.4%2B-777BB4?style=flat-square&logo=php&logoColor=white" alt="PHP Version">
  </a>
  <a href="LICENSE">
    <img src="https://img.shields.io/badge/License-MIT-green?style=flat-square" alt="License">
  </a>
  <a href="https://github.com/waleedelsefy/ar-php/stargazers">
    <img src="https://img.shields.io/github/stars/waleedelsefy/ar-php?style=flat-square&color=9A1F2C" alt="Stars">
  </a>
</p>

<p align="center">
  <strong>مكتبة PHP شاملة ومفتوحة المصدر لمعالجة اللغة العربية</strong><br>
  Comprehensive open-source PHP library for Arabic language processing
</p>

<p align="center">
  <a href="#-installation">Installation</a> •
  <a href="#-quick-start">Quick Start</a> •
  <a href="#-modules">Modules</a> •
  <a href="#-examples">Examples</a> •
  <a href="#-contributing">Contributing</a>
</p>

---

## ✨ Features | المميزات

- 🚀 **20+ Modules** - وحدات متكاملة لكل احتياجات معالجة العربية
- 🎯 **PHP 8.4+** - مبني على أحدث مميزات PHP
- 📦 **Zero Dependencies** - بدون اعتمادات خارجية
- 🔒 **100% Type-Safe** - كود آمن بالكامل
- ⚡ **High Performance** - أداء عالي ومحسن
- 📚 **Well Documented** - توثيق شامل مع أمثلة
- 🧪 **Fully Tested** - اختبارات شاملة
- 🌍 **Open Source** - MIT License

---

## 📦 Installation | التثبيت

```bash
composer require waleedelsefy/ar-php
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

use ArPHP\Core\Modules\Normalization\Normalizer;
use ArPHP\Core\Modules\ArabicTokenizer\Tokenizer;
use ArPHP\Core\Modules\Sentiment\Sentiment;
use ArPHP\Core\Modules\ArabicSoundex\ArabicSoundex;

// 🔤 Normalize Arabic text | تطبيع النص العربي
$normalized = Normalizer::normalize('أَحْمَدُ وَإِبْرَاهِيمُ');
// Output: احمد وابراهيم

// ✂️ Tokenize text | تقطيع النص
$tokens = Tokenizer::tokenize('مرحباً بكم في مكتبة عربي PHP');
// Output: ['مرحباً', 'بكم', 'في', 'مكتبة', 'عربي', 'PHP']

// 💭 Sentiment analysis | تحليل المشاعر
$sentiment = Sentiment::analyze('هذا المنتج رائع وممتاز!');
// Output: ['label' => 'positive', 'score' => 0.85, 'confidence' => 0.92]

// 🔊 Romanization | الكتابة الصوتية
$roman = ArabicSoundex::romanize('محمد');
// Output: Muhammad
```

---

## 📚 Modules | الوحدات

### 📝 Text Processing | معالجة النصوص

| Module | Description | الوصف |
|--------|-------------|-------|
| **Normalizer** | Text normalization | تطبيع النص العربي |
| **Tokenizer** | Text tokenization | تقطيع النص لكلمات |
| **Tashkeel** | Diacritics handling | التعامل مع التشكيل |
| **Slugify** | URL-safe slugs | توليد روابط صديقة |
| **Stopwords** | Stopword filtering | تصفية كلمات الوقف |

### 🧠 NLP & Analysis | تحليل اللغة

| Module | Description | الوصف |
|--------|-------------|-------|
| **Sentiment** | Sentiment analysis | تحليل المشاعر |
| **NER** | Named Entity Recognition | التعرف على الكيانات |
| **WordFrequency** | Word frequency analysis | تحليل تكرار الكلمات |
| **Summarizer** | Text summarization | تلخيص النصوص |
| **Lemmatizer** | Root extraction | استخراج الجذور |

### 🔤 Transliteration | التحويل الصوتي

| Module | Description | الوصف |
|--------|-------------|-------|
| **ArabicSoundex** | Phonetic matching & Romanization | المطابقة الصوتية والكتابة اللاتينية |
| **Buckwalter** | Buckwalter transliteration | نظام بكوالتر |
| **Keyboard** | Keyboard layout fix | تصحيح لوحة المفاتيح |

### 🔢 Numbers & Dates | الأرقام والتواريخ

| Module | Description | الوصف |
|--------|-------------|-------|
| **Numbers** | Number to Arabic words | تحويل الأرقام لكلمات |
| **Hijri** | Hijri calendar | التقويم الهجري |
| **PrayerTimes** | Prayer times | أوقات الصلاة |

### 👤 Names & Gender | الأسماء والجنس

| Module | Description | الوصف |
|--------|-------------|-------|
| **Gender** | Gender detection | تحديد جنس الاسم |
| **NameParser** | Arabic name parsing | تحليل الأسماء العربية |

---

## 💡 Examples | أمثلة

### Sentiment Analysis | تحليل المشاعر

```php
use ArPHP\Core\Modules\Sentiment\Sentiment;

// تحليل نص إيجابي
$result = Sentiment::analyze('الفيلم كان رائعاً والتمثيل ممتاز');
// [
//     'label' => 'positive',
//     'score' => 0.87,
//     'confidence' => 0.94
// ]

// تحليل نص سلبي
$result = Sentiment::analyze('الخدمة سيئة جداً والموظفين غير متعاونين');
// [
//     'label' => 'negative',
//     'score' => -0.82,
//     'confidence' => 0.91
// ]

// تحليل مفصل
$breakdown = Sentiment::breakdown($text);
// [
//     'positive_words' => ['رائع', 'ممتاز'],
//     'negative_words' => [],
//     'positive_count' => 2,
//     'negative_count' => 0
// ]
```

### Numbers to Arabic Words | تحويل الأرقام

```php
use ArPHP\Core\Modules\NumbersModule;

$numbers = new NumbersModule();
$numbers->register();

// تحويل رقم لكلمات
echo $numbers->toWords('123');
// Output: مائة وثلاثة وعشرون

echo $numbers->toWords('1000000');
// Output: مليون

echo $numbers->toWords('2500');
// Output: ألفان وخمسمائة

// تحويل للأرقام الهندية
echo $numbers->toArabicIndic('123456');
// Output: ١٢٣٤٥٦
```

### Gender Detection | تحديد الجنس

```php
use ArPHP\Core\Modules\GenderModule;

$gender = new GenderModule();
$gender->register();

// تحديد جنس اسم
$result = $gender->detect('محمد');
// [
//     'gender' => 'male',
//     'gender_ar' => 'ذكر',
//     'confidence' => 0.98,
//     'method' => 'database'
// ]

$result = $gender->detect('فاطمة');
// [
//     'gender' => 'female',
//     'gender_ar' => 'أنثى',
//     'confidence' => 0.99,
//     'method' => 'database'
// ]
```

### Romanization | الكتابة الصوتية

```php
use ArPHP\Core\Modules\ArabicSoundex\ArabicSoundex;

// تحويل الأسماء للاتينية
echo ArabicSoundex::romanize('محمد');      // Muhammad
echo ArabicSoundex::romanize('أحمد');      // Ahmad
echo ArabicSoundex::romanize('فاطمة');     // Fatima
echo ArabicSoundex::romanize('عبدالله');   // Abdullah
echo ArabicSoundex::romanize('خديجة');     // Khadija

// مقارنة صوتية
$similar = ArabicSoundex::soundsLike('محمد', 'محمود');
// true - متشابهان صوتياً

$similarity = ArabicSoundex::similarity('أحمد', 'احمد');
// 95 - نسبة التشابه

// البحث عن أسماء متشابهة
$matches = ArabicSoundex::findSimilar('محمد', ['محمود', 'أحمد', 'خالد'], 70);
// ['محمود' => 85]
```

### Text Normalization | تطبيع النص

```php
use ArPHP\Core\Modules\Normalization\Normalizer;

// تطبيع كامل
$text = Normalizer::normalize('أَحْمَدُ وَإِبْرَاهِيمُ وَآدَم');
// Output: احمد وابراهيم وادم

// إزالة التشكيل فقط
$text = Normalizer::removeDiacritics('مُحَمَّدٌ رَسُولُ اللهِ');
// Output: محمد رسول الله

// تطبيع الألف فقط
$text = Normalizer::normalizeAlef('أإآٱا');
// Output: ااااا

// إزالة التطويل
$text = Normalizer::removeTatweel('مـــرحـــبـــاً');
// Output: مرحباً
```

### Tokenization | تقطيع النص

```php
use ArPHP\Core\Modules\ArabicTokenizer\Tokenizer;

// تقطيع لكلمات
$tokens = Tokenizer::tokenize('مرحباً بكم في المكتبة');
// ['مرحباً', 'بكم', 'في', 'المكتبة']

// تقطيع لجمل
$sentences = Tokenizer::sentences('الجملة الأولى. الجملة الثانية؟ الجملة الثالثة!');
// ['الجملة الأولى', 'الجملة الثانية', 'الجملة الثالثة']

// إحصائيات
$wordCount = Tokenizer::wordCount($text);
$charCount = Tokenizer::charCount($text);
```

### NER - Named Entity Recognition | التعرف على الكيانات

```php
use ArPHP\Core\Modules\NER\NER;

$text = 'زار الرئيس محمد بن سلمان مدينة القاهرة والتقى بممثلي شركة أرامكو';

// استخراج كل الكيانات
$entities = NER::extract($text);
// [
//     ['entity' => 'محمد بن سلمان', 'type' => 'PERSON'],
//     ['entity' => 'القاهرة', 'type' => 'LOCATION'],
//     ['entity' => 'أرامكو', 'type' => 'ORGANIZATION']
// ]

// استخراج الأشخاص فقط
$names = NER::names($text);
// ['محمد بن سلمان']

// استخراج الأماكن فقط
$locations = NER::locations($text);
// ['القاهرة']
```

### Slugify | توليد الروابط

```php
use ArPHP\Core\Modules\Slugify\Slugify;

// توليد slug من نص عربي
$slug = Slugify::make('مرحباً بكم في موقعنا');
// Output: mrhba-bkm-fy-mwqena

// الحفاظ على العربي
$slug = Slugify::arabic('مرحباً بكم');
// Output: مرحبا-بكم

// Transliteration
$trans = Slugify::transliterate('محمد أحمد');
// Output: mhmd-ahmd
```

---

## 🏗️ Architecture | البنية

```
ar-php/
├── packages/
│   └── core/
│       └── src/
│           ├── Arabic.php              # Main facade
│           ├── AbstractModule.php      # Base module class
│           ├── ServiceContainer.php    # DI container
│           ├── ModuleRegistry.php      # Module registry
│           ├── Contracts/              # Interfaces
│           └── Modules/                # All modules
│               ├── Sentiment/
│               ├── Normalization/
│               ├── ArabicSoundex/
│               ├── Gender/
│               └── ...
├── website/                            # Demo website
├── tests/                              # Unit tests
├── docs/                               # Documentation
└── examples/                           # Usage examples
```

---

## 🧪 Testing | الاختبارات

```bash
# Run all tests
composer test

# Run with coverage
composer test-coverage

# Run specific test
./vendor/bin/phpunit tests/Unit/SentimentTest.php
```

---

## 🤝 Contributing | المساهمة

نرحب بمساهماتكم! يرجى قراءة [دليل المساهمة](CONTRIBUTING.md) قبل تقديم Pull Request.

1. Fork the repository
2. Create feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open Pull Request

---

## 📄 License | الرخصة

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

---


## ⭐ Support | الدعم

إذا أعجبتك المكتبة، يرجى إعطاء نجمة ⭐ على GitHub!

If you find this library useful, please give it a star ⭐ on GitHub!

---

<p align="center">
  Made with ❤️ for the Arabic-speaking developer community
  <br>
  صُنع بـ ❤️ لمجتمع المطورين العرب
</p>
