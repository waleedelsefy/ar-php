<?php

require_once __DIR__ . '/../vendor/autoload.php';

use ArPHP\Core\Arabic;
use ArPHP\Core\AbstractModule;

/**
 * Multi-AI Provider Example
 * 
 * Demonstrates using Gemini, OpenAI, and Claude together
 */

// ============================================
// AI Service Classes
// ============================================

class GeminiService
{
    private string $apiKey;
    
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }
    
    public function analyze(string $text): array
    {
        return [
            'provider' => 'Gemini',
            'model' => 'gemini-pro',
            'text' => $text,
            'sentiment' => 'positive',
            'confidence' => 0.92
        ];
    }
}

class OpenAIService
{
    private string $apiKey;
    
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }
    
    public function analyze(string $text): array
    {
        return [
            'provider' => 'OpenAI',
            'model' => 'gpt-4',
            'text' => $text,
            'sentiment' => 'positive',
            'confidence' => 0.89
        ];
    }
}

class ClaudeService
{
    private string $apiKey;
    
    public function __construct(string $apiKey)
    {
        $this->apiKey = $apiKey;
    }
    
    public function analyze(string $text): array
    {
        return [
            'provider' => 'Claude',
            'model' => 'claude-3-opus',
            'text' => $text,
            'sentiment' => 'positive',
            'confidence' => 0.94
        ];
    }
}

// ============================================
// AI Aggregator - Compare Results
// ============================================

class AIAggregator
{
    private array $providers = [];
    
    public function addProvider(string $name, object $service): void
    {
        $this->providers[$name] = $service;
    }
    
    public function analyzeWithAll(string $text): array
    {
        $results = [];
        
        foreach ($this->providers as $name => $service) {
            $results[$name] = $service->analyze($text);
        }
        
        return $results;
    }
    
    public function getBestResult(array $results): array
    {
        $best = null;
        $maxConfidence = 0;
        
        foreach ($results as $provider => $result) {
            if ($result['confidence'] > $maxConfidence) {
                $maxConfidence = $result['confidence'];
                $best = $result;
            }
        }
        
        return $best ?? [];
    }
    
    public function getConsensus(array $results): array
    {
        $sentiments = array_column($results, 'sentiment');
        $avgConfidence = array_sum(array_column($results, 'confidence')) / count($results);
        
        $consensus = array_count_values($sentiments);
        arsort($consensus);
        
        return [
            'consensus_sentiment' => array_key_first($consensus),
            'average_confidence' => round($avgConfidence, 2),
            'agreement' => reset($consensus) / count($results),
            'providers_count' => count($results)
        ];
    }
}

// ============================================
// Module Definitions
// ============================================

class GeminiModule extends AbstractModule
{
    public function getName(): string
    {
        return 'gemini';
    }
    
    public function register(): void
    {
        Arabic::container()->register('gemini', function() {
            $apiKey = getenv('GEMINI_API_KEY') ?: 'demo-gemini-key';
            return new GeminiService($apiKey);
        });
        
        echo "✅ Gemini module registered\n";
    }
    
    public function boot(): void
    {
        echo "🟢 Gemini ready\n";
    }
}

class OpenAIModule extends AbstractModule
{
    public function getName(): string
    {
        return 'openai';
    }
    
    public function register(): void
    {
        Arabic::container()->register('openai', function() {
            $apiKey = getenv('OPENAI_API_KEY') ?: 'demo-openai-key';
            return new OpenAIService($apiKey);
        });
        
        echo "✅ OpenAI module registered\n";
    }
    
    public function boot(): void
    {
        echo "🟢 OpenAI ready\n";
    }
}

class ClaudeModule extends AbstractModule
{
    public function getName(): string
    {
        return 'claude';
    }
    
    public function register(): void
    {
        Arabic::container()->register('claude', function() {
            $apiKey = getenv('CLAUDE_API_KEY') ?: 'demo-claude-key';
            return new ClaudeService($apiKey);
        });
        
        echo "✅ Claude module registered\n";
    }
    
    public function boot(): void
    {
        echo "🟢 Claude ready\n";
    }
}

class AIAggregatorModule extends AbstractModule
{
    protected array $dependencies = ['gemini', 'openai', 'claude'];
    
    public function getName(): string
    {
        return 'ai-aggregator';
    }
    
    public function register(): void
    {
        Arabic::container()->register('ai-aggregator', function($container) {
            $aggregator = new AIAggregator();
            
            // Add all AI providers
            $aggregator->addProvider('gemini', $container->get('gemini'));
            $aggregator->addProvider('openai', $container->get('openai'));
            $aggregator->addProvider('claude', $container->get('claude'));
            
            return $aggregator;
        });
        
        echo "✅ AI Aggregator registered\n";
    }
    
    public function boot(): void
    {
        echo "🎯 AI Aggregator ready (combines all AI providers)\n";
    }
}

// ============================================
// Initialize with All AI Modules
// ============================================

echo "\n" . str_repeat('=', 60) . "\n";
echo "🚀 Initializing Multi-AI System\n";
echo str_repeat('=', 60) . "\n\n";

Arabic::init([
    new GeminiModule(),
    new OpenAIModule(),
    new ClaudeModule(),
    new AIAggregatorModule(),  // Depends on the 3 above
]);

echo "\n" . str_repeat('=', 60) . "\n";
echo "📊 Using Multiple AI Providers\n";
echo str_repeat('=', 60) . "\n\n";

// Test text
$arabicText = "هذا المنتج رائع جداً وأنصح الجميع بتجربته!";

// ============================================
// Method 1: Use Each Provider Individually
// ============================================

echo "1️⃣ Individual Provider Results:\n";
echo str_repeat('-', 60) . "\n\n";

$gemini = Arabic::container()->get('gemini');
$openai = Arabic::container()->get('openai');
$claude = Arabic::container()->get('claude');

echo "🔷 Gemini Analysis:\n";
$geminiResult = $gemini->analyze($arabicText);
print_r($geminiResult);

echo "\n🔶 OpenAI Analysis:\n";
$openaiResult = $openai->analyze($arabicText);
print_r($openaiResult);

echo "\n🔵 Claude Analysis:\n";
$claudeResult = $claude->analyze($arabicText);
print_r($claudeResult);

// ============================================
// Method 2: Use Aggregator for All at Once
// ============================================

echo "\n" . str_repeat('=', 60) . "\n";
echo "2️⃣ Aggregated Analysis (All Providers):\n";
echo str_repeat('-', 60) . "\n\n";

$aggregator = Arabic::container()->get('ai-aggregator');

// Analyze with all providers
$allResults = $aggregator->analyzeWithAll($arabicText);

echo "📊 All Results:\n";
foreach ($allResults as $provider => $result) {
    echo "  • {$provider}: {$result['sentiment']} ({$result['confidence']})\n";
}

// Get best result
echo "\n🏆 Best Result (Highest Confidence):\n";
$best = $aggregator->getBestResult($allResults);
print_r($best);

// Get consensus
echo "\n🎯 Consensus Analysis:\n";
$consensus = $aggregator->getConsensus($allResults);
print_r($consensus);

// ============================================
// Method 3: Fallback Strategy
// ============================================

echo "\n" . str_repeat('=', 60) . "\n";
echo "3️⃣ Fallback Strategy (Try providers in order):\n";
echo str_repeat('-', 60) . "\n\n";

function analyzeWithFallback(string $text): array
{
    $providers = ['gemini', 'openai', 'claude'];
    
    foreach ($providers as $provider) {
        try {
            echo "Trying {$provider}... ";
            $service = Arabic::container()->get($provider);
            $result = $service->analyze($text);
            echo "✅ Success!\n";
            return $result;
        } catch (Exception $e) {
            echo "❌ Failed, trying next...\n";
            continue;
        }
    }
    
    throw new Exception("All AI providers failed");
}

$fallbackResult = analyzeWithFallback($arabicText);
echo "\nResult: {$fallbackResult['provider']} - {$fallbackResult['sentiment']}\n";

// ============================================
// Summary
// ============================================

echo "\n" . str_repeat('=', 60) . "\n";
echo "✨ Multi-AI Integration Complete!\n";
echo str_repeat('=', 60) . "\n\n";

echo "📌 Summary:\n";
echo "  • 3 AI Providers: Gemini, OpenAI, Claude\n";
echo "  • All working together seamlessly\n";
echo "  • Aggregator combines results\n";
echo "  • Fallback strategy for reliability\n";
echo "  • Modular architecture = Easy to add more providers!\n";
