<?php

require_once __DIR__ . '/../vendor/autoload.php';

use ArPHP\Core\Arabic;
use ArPHP\Core\AbstractModule;

/**
 * AI Module Example - Integration with Gemini API
 * 
 * This demonstrates how to integrate AI capabilities into ArPHP
 */

// AI Service that wraps Gemini API
class GeminiAIService
{
    private string $apiKey;
    private string $model;
    
    public function __construct(string $apiKey, string $model = 'gemini-pro')
    {
        $this->apiKey = $apiKey;
        $this->model = $model;
    }
    
    /**
     * Analyze Arabic text using Gemini
     */
    public function analyzeText(string $text, string $task): array
    {
        // In real implementation, call Gemini API
        // This is just a demonstration structure
        
        return [
            'task' => $task,
            'text' => $text,
            'model' => $this->model,
            'result' => 'AI analysis would go here',
            'timestamp' => date('Y-m-d H:i:s')
        ];
    }
    
    /**
     * Translate Arabic text
     */
    public function translate(string $text, string $targetLang): string
    {
        // Call Gemini API for translation
        return "Translated: {$text} to {$targetLang}";
    }
    
    /**
     * Sentiment analysis for Arabic
     */
    public function sentimentAnalysis(string $text): array
    {
        // Call Gemini API for sentiment
        return [
            'sentiment' => 'positive',
            'confidence' => 0.95,
            'text' => $text
        ];
    }
    
    /**
     * Extract keywords from Arabic text
     */
    public function extractKeywords(string $text): array
    {
        // Call Gemini API
        return ['keyword1', 'keyword2', 'keyword3'];
    }
}

// AI Module
class GeminiModule extends AbstractModule
{
    protected string $version = '1.0.0';
    
    public function getName(): string
    {
        return 'gemini-ai';
    }
    
    public function register(): void
    {
        // Register AI service in container
        Arabic::container()->register('ai', function() {
            // In production, get API key from config/env
            $apiKey = getenv('GEMINI_API_KEY') ?: 'your-api-key-here';
            return new GeminiAIService($apiKey);
        });
        
        echo "✅ Gemini AI module registered\n";
    }
    
    public function boot(): void
    {
        echo "🤖 Gemini AI module ready\n";
    }
}

// Initialize ArPHP with AI Module
Arabic::init([
    new GeminiModule(),
]);

echo "\n" . str_repeat('=', 50) . "\n";
echo "📝 Using AI Services\n";
echo str_repeat('=', 50) . "\n\n";

// Get AI service from container
$ai = Arabic::container()->get('ai');

// Example 1: Analyze text
echo "1️⃣ Text Analysis:\n";
$analysis = $ai->analyzeText('النص العربي للتحليل', 'grammar_check');
print_r($analysis);

// Example 2: Translation
echo "\n2️⃣ Translation:\n";
$translation = $ai->translate('مرحباً بك', 'en');
echo "Result: {$translation}\n";

// Example 3: Sentiment Analysis
echo "\n3️⃣ Sentiment Analysis:\n";
$sentiment = $ai->sentimentAnalysis('هذا منتج رائع جداً!');
print_r($sentiment);

// Example 4: Keyword Extraction
echo "\n4️⃣ Keyword Extraction:\n";
$keywords = $ai->extractKeywords('البرمجة بلغة PHP مع الذكاء الاصطناعي');
echo "Keywords: " . implode(', ', $keywords) . "\n";

echo "\n✨ AI integration working perfectly!\n";
