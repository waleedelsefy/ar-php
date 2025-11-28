<?php

require_once __DIR__ . '/../vendor/autoload.php';

use ArPHP\Core\Arabic;
use ArPHP\Core\AbstractModule;

/**
 * Model Selection Example
 * 
 * Shows how to select specific AI models within each provider
 */

// ============================================
// Flexible AI Service with Model Selection
// ============================================

class FlexibleAIService
{
    private string $provider;
    private string $model;
    private string $apiKey;
    private array $config;
    
    public function __construct(
        string $provider,
        string $model,
        string $apiKey,
        array $config = []
    ) {
        $this->provider = $provider;
        $this->model = $model;
        $this->apiKey = $apiKey;
        $this->config = $config;
    }
    
    public function getProvider(): string
    {
        return $this->provider;
    }
    
    public function getModel(): string
    {
        return $this->model;
    }
    
    public function analyze(string $text): array
    {
        return [
            'provider' => $this->provider,
            'model' => $this->model,
            'text' => $text,
            'result' => "Analysis from {$this->model}",
            'config' => $this->config,
        ];
    }
}

// ============================================
// Configurable AI Module
// ============================================

class ConfigurableAIModule extends AbstractModule
{
    private string $provider;
    private string $model;
    private array $config;
    
    public function __construct(
        string $provider,
        string $model,
        array $config = []
    ) {
        $this->provider = $provider;
        $this->model = $model;
        $this->config = $config;
    }
    
    public function getName(): string
    {
        return "ai-{$this->provider}-{$this->model}";
    }
    
    public function register(): void
    {
        $serviceName = "{$this->provider}-{$this->model}";
        
        Arabic::container()->register($serviceName, function() {
            $apiKey = getenv(strtoupper($this->provider) . '_API_KEY') ?: 'demo-key';
            
            return new FlexibleAIService(
                $this->provider,
                $this->model,
                $apiKey,
                $this->config
            );
        });
        
        echo "✅ {$this->provider} ({$this->model}) registered\n";
    }
    
    public function boot(): void
    {
        echo "🚀 {$this->provider}/{$this->model} ready\n";
    }
}

// ============================================
// Initialize with Specific Models
// ============================================

echo "\n" . str_repeat('=', 70) . "\n";
echo "🎯 Model Selection Demo\n";
echo str_repeat('=', 70) . "\n\n";

echo "📝 Registering Multiple Models:\n";
echo str_repeat('-', 70) . "\n\n";

Arabic::init([
    // Gemini Models
    new ConfigurableAIModule('gemini', 'gemini-pro', [
        'temperature' => 0.7,
        'max_tokens' => 1000
    ]),
    new ConfigurableAIModule('gemini', 'gemini-pro-vision', [
        'temperature' => 0.5
    ]),
    new ConfigurableAIModule('gemini', 'gemini-3-pro', [
        'temperature' => 0.7,
        'max_tokens' => 2000
    ]),
    
    // OpenAI Models
    new ConfigurableAIModule('openai', 'gpt-3.5-turbo', [
        'temperature' => 0.9,
        'max_tokens' => 1500
    ]),
    new ConfigurableAIModule('openai', 'gpt-4', [
        'temperature' => 0.8,
        'max_tokens' => 2000
    ]),
    new ConfigurableAIModule('openai', 'gpt-4-turbo', [
        'temperature' => 0.7,
        'max_tokens' => 4000
    ]),
    new ConfigurableAIModule('openai', 'chatgpt-5', [
        'temperature' => 0.7,
        'max_tokens' => 8000
    ]),
    new ConfigurableAIModule('openai', 'chatgpt-5.1', [
        'temperature' => 0.7,
        'max_tokens' => 10000
    ]),
    
    // Claude Models
    new ConfigurableAIModule('claude', 'claude-3-haiku', [
        'temperature' => 0.8,
        'max_tokens' => 2000
    ]),
    new ConfigurableAIModule('claude', 'claude-3-sonnet', [
        'temperature' => 0.7,
        'max_tokens' => 3000
    ]),
    new ConfigurableAIModule('claude', 'claude-3-opus', [
        'temperature' => 0.6,
        'max_tokens' => 4000
    ]),
    new ConfigurableAIModule('claude', 'claude-sonnet-4', [
        'temperature' => 0.6,
        'max_tokens' => 5000
    ]),
    new ConfigurableAIModule('claude', 'claude-sonnet-4.5', [
        'temperature' => 0.6,
        'max_tokens' => 6000
    ]),
    
    // Free AI Models (No API key required or free tier)
    new ConfigurableAIModule('huggingface', 'llama-3.1-8b', [
        'temperature' => 0.7,
        'max_tokens' => 4000
    ]),
    new ConfigurableAIModule('mistral', 'mistral-7b', [
        'temperature' => 0.7,
        'max_tokens' => 8000
    ]),
    new ConfigurableAIModule('cohere', 'command-r', [
        'temperature' => 0.7,
        'max_tokens' => 4000
    ]),
    new ConfigurableAIModule('deepseek', 'deepseek-chat', [
        'temperature' => 0.7,
        'max_tokens' => 4000
    ]),
]);

echo "\n" . str_repeat('=', 70) . "\n";
echo "🔍 Using Specific Models\n";
echo str_repeat('=', 70) . "\n\n";

$text = "مرحباً! كيف يمكنني مساعدتك اليوم؟";

// ============================================
// Method 1: Direct Model Selection
// ============================================

echo "1️⃣ Direct Model Selection:\n";
echo str_repeat('-', 70) . "\n\n";

// Use specific Gemini model
$geminiPro = Arabic::container()->get('gemini-gemini-pro');
echo "🔷 Gemini Pro:\n";
$result = $geminiPro->analyze($text);
print_r($result);

// Use specific OpenAI model
$gpt4 = Arabic::container()->get('openai-gpt-4');
echo "\n🔶 GPT-4:\n";
$result = $gpt4->analyze($text);
print_r($result);

// Use specific Claude model
$claudeOpus = Arabic::container()->get('claude-claude-3-opus');
echo "\n🔵 Claude 3 Opus:\n";
$result = $claudeOpus->analyze($text);
print_r($result);

// ============================================
// Method 2: Compare Different Models
// ============================================

echo "\n" . str_repeat('=', 70) . "\n";
echo "2️⃣ Compare Models from Same Provider:\n";
echo str_repeat('-', 70) . "\n\n";

echo "📊 OpenAI Models Comparison:\n\n";

$openaiModels = [
    'gpt-3.5-turbo',
    'gpt-4',
    'gpt-4-turbo',
    'chatgpt-5',
    'chatgpt-5.1'
];

foreach ($openaiModels as $model) {
    $service = Arabic::container()->get("openai-{$model}");
    $result = $service->analyze("اختبار المقارنة");
    echo "  • {$model}: {$result['model']}\n";
    echo "    Config: " . json_encode($result['config']) . "\n";
}

echo "\n📊 Claude Models Comparison:\n\n";

$claudeModels = [
    'claude-3-haiku',      // Fastest, cheapest
    'claude-3-sonnet',     // Balanced
    'claude-3-opus',       // Very capable
    'claude-sonnet-4',     // New generation
    'claude-sonnet-4.5'    // Latest & most capable
];

foreach ($claudeModels as $model) {
    $service = Arabic::container()->get("claude-{$model}");
    $result = $service->analyze("اختبار المقارنة");
    echo "  • {$model}: {$result['model']}\n";
    echo "    Config: " . json_encode($result['config']) . "\n";
}

echo "\n📊 Free AI Models Comparison:\n\n";

$freeModels = [
    'huggingface-llama-3.1-8b',  // Free on HuggingFace
    'mistral-mistral-7b',         // Open source
    'cohere-command-r',           // Free tier
    'deepseek-deepseek-chat'      // Free API
];

foreach ($freeModels as $model) {
    $service = Arabic::container()->get($model);
    $result = $service->analyze("اختبار المقارنة");
    echo "  • {$model}: {$result['model']} 🆓 FREE\n";
    echo "    Config: " . json_encode($result['config']) . "\n";
}

// ============================================
// Method 3: Model Router (Smart Selection)
// ============================================

echo "\n" . str_repeat('=', 70) . "\n";
echo "3️⃣ Smart Model Router:\n";
echo str_repeat('-', 70) . "\n\n";

class ModelRouter
{
    public static function selectModel(string $task, string $priority = 'balanced'): string
    {
        return match($priority) {
            'fast' => match($task) {
                'simple' => 'openai-gpt-3.5-turbo',
                'complex' => 'claude-claude-3-haiku',
                default => 'gemini-gemini-pro'
            },
            'quality' => match($task) {
                'simple' => 'openai-gpt-4',
                'complex' => 'claude-claude-3-opus',
                default => 'openai-gpt-4-turbo'
            },
            'balanced' => match($task) {
                'simple' => 'gemini-gemini-pro',
                'complex' => 'claude-claude-3-sonnet',
                default => 'openai-gpt-4'
            },
            default => 'gemini-gemini-pro'
        };
    }
}

$scenarios = [
    ['task' => 'simple', 'priority' => 'fast', 'text' => 'ترجم: Hello'],
    ['task' => 'complex', 'priority' => 'quality', 'text' => 'تحليل نحوي معقد'],
    ['task' => 'medium', 'priority' => 'balanced', 'text' => 'تلخيص النص'],
];

foreach ($scenarios as $scenario) {
    $selectedModel = ModelRouter::selectModel($scenario['task'], $scenario['priority']);
    $service = Arabic::container()->get($selectedModel);
    
    echo "Task: {$scenario['task']} | Priority: {$scenario['priority']}\n";
    echo "  → Selected: {$service->getModel()}\n";
    echo "  → Text: {$scenario['text']}\n\n";
}

// ============================================
// Method 4: Cost-Based Selection
// ============================================

echo "\n" . str_repeat('=', 70) . "\n";
echo "4️⃣ Cost-Based Model Selection:\n";
echo str_repeat('-', 70) . "\n\n";

class CostOptimizer
{
    private static array $costs = [
        // Free Models
        'huggingface-llama-3.1-8b' => 0.0,      // FREE
        'mistral-mistral-7b' => 0.0,            // FREE (open source)
        'cohere-command-r' => 0.0,              // FREE tier
        'deepseek-deepseek-chat' => 0.0,        // FREE API
        // Paid Models
        'claude-claude-3-haiku' => 0.00025,
        'gemini-gemini-pro' => 0.0005,
        'openai-gpt-3.5-turbo' => 0.001,
        'gemini-gemini-3-pro' => 0.002,
        'claude-claude-3-sonnet' => 0.003,
        'openai-gpt-4-turbo' => 0.01,
        'claude-claude-3-opus' => 0.015,
        'claude-claude-sonnet-4' => 0.018,
        'openai-gpt-4' => 0.03,
        'claude-claude-sonnet-4.5' => 0.025,
        'openai-chatgpt-5' => 0.035,
        'openai-chatgpt-5.1' => 0.04,
    ];
    
    public static function selectByBudget(float $budget): array
    {
        $available = [];
        
        foreach (self::$costs as $model => $cost) {
            if ($cost <= $budget) {
                $available[$model] = $cost;
            }
        }
        
        asort($available);
        return $available;
    }
}

$budget = 0.005;
echo "Budget: \${$budget} per request\n\n";

$availableModels = CostOptimizer::selectByBudget($budget);

echo "Available models within budget:\n";
foreach ($availableModels as $model => $cost) {
    echo "  • {$model}: \${$cost}\n";
}

// Select best model within budget
$bestModel = array_key_last($availableModels);
echo "\n✅ Selected: {$bestModel}\n";

$service = Arabic::container()->get($bestModel);
$result = $service->analyze("تحليل اقتصادي");
echo "Result: {$result['model']}\n";

// ============================================
// Method 5: Feature-Based Selection
// ============================================

echo "\n" . str_repeat('=', 70) . "\n";
echo "5️⃣ Feature-Based Model Selection:\n";
echo str_repeat('-', 70) . "\n\n";

$features = [
    'vision' => ['gemini-gemini-pro-vision'],
    'long_context' => ['openai-gpt-4-turbo', 'claude-claude-3-opus', 'openai-chatgpt-5', 'openai-chatgpt-5.1', 'claude-claude-sonnet-4.5', 'mistral-mistral-7b'],
    'fast_response' => ['openai-gpt-3.5-turbo', 'claude-claude-3-haiku', 'mistral-mistral-7b'],
    'multilingual' => ['openai-gpt-4', 'gemini-gemini-pro', 'gemini-gemini-3-pro', 'claude-claude-3-opus', 'openai-chatgpt-5.1', 'cohere-command-r'],
    'latest_models' => ['claude-claude-sonnet-4.5', 'openai-chatgpt-5.1', 'gemini-gemini-3-pro', 'deepseek-deepseek-chat'],
    'free' => ['huggingface-llama-3.1-8b', 'mistral-mistral-7b', 'cohere-command-r', 'deepseek-deepseek-chat'],
    'open_source' => ['huggingface-llama-3.1-8b', 'mistral-mistral-7b'],
];

echo "📋 Available Features:\n\n";
foreach ($features as $feature => $models) {
    echo "  • {$feature}:\n";
    foreach ($models as $model) {
        echo "    - {$model}\n";
    }
}

// Select model with specific feature
$requiredFeature = 'long_context';
echo "\n🎯 Need: {$requiredFeature}\n";
echo "Available models:\n";
foreach ($features[$requiredFeature] as $model) {
    echo "  • {$model}\n";
}

// ============================================
// Summary
// ============================================

echo "\n" . str_repeat('=', 70) . "\n";
echo "✨ Model Selection Complete!\n";
echo str_repeat('=', 70) . "\n\n";

echo "📌 Summary:\n";
echo "  ✅ Direct model selection: Access any model directly\n";
echo "  ✅ Model comparison: Compare models side-by-side\n";
echo "  ✅ Smart routing: Auto-select based on task\n";
echo "  ✅ Cost optimization: Select by budget\n";
echo "  ✅ Feature-based: Choose models with specific features\n";
echo "\n  🎯 Total flexibility in model selection!\n";
