# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.0] - 2025-11-28

### Added
- Initial release of ArPHP Enhanced
- Modular architecture with `ModuleInterface` and `AbstractModule`
- PSR-11 compliant `ServiceContainer` for dependency injection
- `ModuleRegistry` for managing module lifecycle
- Automatic dependency resolution between modules
- Circular dependency detection
- Custom exceptions: `ModuleNotFoundException`, `ServiceNotFoundException`, `CircularDependencyException`

#### Arabic Text Processing Modules
- **TashkeelModule**: Add, remove, normalize Arabic diacritics (tashkeel)
- **TransliterationModule**: Arabic ↔ Latin conversion with 3 standards (ALA-LC, Buckwalter, ISO-233)
- **NumbersModule**: Arabic-Indic ↔ Western numerals, number-to-words conversion
- **TextCleanerModule**: Clean HTML, URLs, normalize text, word/character counting
- **StemmingModule**: Arabic root extraction (light stemmer) with prefix/suffix removal
- **SentimentModule**: Dictionary-based sentiment analysis (40 words, intensifiers, negation)
- **KeyboardModule**: Fix Arabic/English keyboard layout mistakes with auto-detection
- **StatisticsModule**: Text analysis (word count, readability score, lexical diversity)

#### Performance Features
- Batch processing support on 6 modules (Tashkeel, Transliteration, Stemming, Sentiment)
- Multiple transliteration standards (ALA-LC, Buckwalter, ISO-233)
- Stateless architecture - no database required

#### Documentation & Quality
- Comprehensive documentation (Getting Started, Architecture, API Reference)
- Full test coverage with PHPUnit (100 tests, 228 assertions)
- PHPStan level 8 static analysis (0 errors)
- Example files: basic usage, custom modules, arabic text processing, advanced features
