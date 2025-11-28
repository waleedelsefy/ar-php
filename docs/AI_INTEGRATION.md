# AI Integration Guide

## Overview

ArPHP Enhanced provides a modular architecture that makes it easy to integrate AI services like Gemini, OpenAI, or any other AI provider.

## Example: Gemini AI Module

### Installation

First, install the Google Generative AI package:

```bash
composer require google/generative-ai-php
```

### Creating the AI Module

```php
<?php

use ArPHP\Core\AbstractModule;
use ArPHP\Core\Arabic;

class GeminiAIModule extends AbstractModule
{
    public function getName(): string
    {
        return 'gemini-ai';
    }
    
    public function register(): void
    {
        Arabic::container()->register('ai', function() {
            $apiKey = getenv('GEMINI_API_KEY');
            return new GeminiAIService($apiKey);
        });
    }
    
    public function boot(): void
    {
        // Module is ready
    }
}
```

### Usage Example

```php
use ArPHP\Core\Arabic;

// Initialize with AI module
Arabic::init([
    new GeminiAIModule()
]);

// Get AI service
$ai = Arabic::container()->get('ai');

// Use AI for Arabic text processing
$result = $ai->analyzeText('النص العربي هنا', 'sentiment');
```

## Supported AI Use Cases

### 1. Sentiment Analysis
```php
$sentiment = $ai->sentimentAnalysis('هذا منتج رائع جداً!');
// Result: ['sentiment' => 'positive', 'confidence' => 0.95]
```

### 2. Text Translation
```php
$translation = $ai->translate('مرحباً بك', 'en');
// Result: "Welcome"
```

### 3. Grammar Checking
```php
$grammar = $ai->checkGrammar('النص العربي للتدقيق');
// Returns corrections and suggestions
```

### 4. Text Summarization
```php
$summary = $ai->summarize($longArabicText);
// Returns concise summary
```

### 5. Keyword Extraction
```php
$keywords = $ai->extractKeywords($text);
// Returns: ['keyword1', 'keyword2', ...]
```

### 6. Named Entity Recognition
```php
$entities = $ai->extractEntities($text);
// Returns: ['persons' => [...], 'places' => [...], 'organizations' => [...]]
```

## Configuration

### Environment Variables

Create a `.env` file:

```env
GEMINI_API_KEY=your_api_key_here
GEMINI_MODEL=gemini-pro
```

### Module Configuration

```php
class GeminiAIModule extends AbstractModule
{
    protected array $config = [
        'model' => 'gemini-pro',
        'temperature' => 0.7,
        'max_tokens' => 1000,
    ];
    
    public function register(): void
    {
        Arabic::container()->register('ai', function() {
            return new GeminiAIService(
                apiKey: getenv('GEMINI_API_KEY'),
                config: $this->config
            );
        });
    }
}
```

## Multiple AI Providers

You can register multiple AI services:

```php
Arabic::init([
    new GeminiModule(),
    new OpenAIModule(),
    new ClaudeModule(),
]);

// Use specific provider
$gemini = Arabic::container()->get('gemini');
$openai = Arabic::container()->get('openai');
$claude = Arabic::container()->get('claude');
```

## Real-World Implementation

### Complete Gemini Service

```php
<?php

use Google\GenerativeAI\Client;

class GeminiAIService
{
    private Client $client;
    private string $model;
    
    public function __construct(string $apiKey, string $model = 'gemini-pro')
    {
        $this->client = new Client($apiKey);
        $this->model = $model;
    }
    
    public function analyzeText(string $text, string $task): array
    {
        $prompt = $this->buildPrompt($text, $task);
        
        $response = $this->client->generateContent([
            'model' => $this->model,
            'prompt' => $prompt,
        ]);
        
        return $this->parseResponse($response);
    }
    
    public function sentimentAnalysis(string $text): array
    {
        $prompt = "قم بتحليل المشاعر في النص التالي: {$text}";
        
        $response = $this->client->generateContent([
            'model' => $this->model,
            'prompt' => $prompt,
        ]);
        
        return $this->parseSentiment($response);
    }
    
    private function buildPrompt(string $text, string $task): string
    {
        return match($task) {
            'sentiment' => "حلل المشاعر في: {$text}",
            'grammar' => "تحقق من القواعد النحوية: {$text}",
            'summary' => "لخص النص التالي: {$text}",
            'keywords' => "استخرج الكلمات المفتاحية من: {$text}",
            default => "حلل النص: {$text}",
        };
    }
}
```

## Benefits

✅ **Modular**: Easy to add/remove AI providers  
✅ **Flexible**: Support multiple AI services simultaneously  
✅ **Type-Safe**: Full PHP 8.1+ type hints  
✅ **Testable**: Mock AI services in tests  
✅ **Configurable**: Environment-based configuration  

## Next Steps

1. Install AI provider package
2. Create AI module extending `AbstractModule`
3. Register AI service in container
4. Use AI service through the container

See `examples/ai-module-example.php` for a complete example.
