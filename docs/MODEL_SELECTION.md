# Model Selection Guide

## Overview

ArPHP Enhanced allows you to select and use specific AI models from any provider with full flexibility.

## Supported Models

### Google Gemini
- `gemini-pro` - Standard model
- `gemini-pro-vision` - With image understanding
- `gemini-3-pro` - Latest generation
- `gemini-ultra` - Most capable (when available)

### OpenAI
- `gpt-3.5-turbo` - Fast and cost-effective
- `gpt-4` - High quality, general purpose
- `gpt-4-turbo` - With longer context
- `gpt-4-vision` - With image understanding
- `chatgpt-5` - Next generation model
- `chatgpt-5.1` - Latest, most advanced

### Anthropic Claude
- `claude-3-haiku` - Fastest, most affordable
- `claude-3-sonnet` - Balanced performance
- `claude-3-opus` - Very capable
- `claude-sonnet-4` - New generation
- `claude-sonnet-4.5` - Latest, most capable

### Free AI Models 🆓

#### Hugging Face (Open Source)
- `llama-3.1-8b` - Meta's Llama 3.1, free inference
- Many other models available

#### Mistral AI (Open Source)
- `mistral-7b` - High performance, open source
- `mixtral-8x7b` - Mixture of experts

#### Cohere (Free Tier)
- `command-r` - Free tier available
- Good for Arabic text

#### DeepSeek (Free API)
- `deepseek-chat` - Free API access
- Strong reasoning capabilities

## Registration

### Register Specific Models

```php
use ArPHP\Core\Arabic;

Arabic::init([
    // Gemini models
    new ConfigurableAIModule('gemini', 'gemini-pro', [
        'temperature' => 0.7,
        'max_tokens' => 1000
    ]),
    new ConfigurableAIModule('gemini', 'gemini-pro-vision'),
    
    // OpenAI models
    new ConfigurableAIModule('openai', 'gpt-4', [
        'temperature' => 0.8,
        'max_tokens' => 2000
    ]),
    new ConfigurableAIModule('openai', 'gpt-3.5-turbo'),
    
    // Claude models
    new ConfigurableAIModule('claude', 'claude-3-opus', [
        'temperature' => 0.6,
        'max_tokens' => 4000
    ]),
]);
```

## Usage

### 1. Direct Model Access

```php
// Access specific model
$gpt4 = Arabic::container()->get('openai-gpt-4');
$result = $gpt4->analyze('النص العربي');

// Access another model
$gemini = Arabic::container()->get('gemini-gemini-pro');
$result = $gemini->analyze('النص العربي');
```

### 2. Compare Models

```php
$models = ['gpt-3.5-turbo', 'gpt-4', 'gpt-4-turbo'];

foreach ($models as $model) {
    $service = Arabic::container()->get("openai-{$model}");
    $result = $service->analyze($text);
    echo "{$model}: {$result['result']}\n";
}
```

### 3. Smart Model Router

```php
class ModelRouter
{
    public static function selectModel(string $task, string $priority): string
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
            'balanced' => 'openai-gpt-4',
            default => 'gemini-gemini-pro'
        };
    }
}

// Use the router
$model = ModelRouter::selectModel('complex', 'quality');
$service = Arabic::container()->get($model);
```

### 4. Cost-Based Selection

```php
class CostOptimizer
{
    private static array $costs = [
        'openai-gpt-3.5-turbo' => 0.001,
        'openai-gpt-4' => 0.03,
        'gemini-gemini-pro' => 0.0005,
        'claude-claude-3-haiku' => 0.00025,
    ];
    
    public static function selectByBudget(float $budget): string
    {
        foreach (self::$costs as $model => $cost) {
            if ($cost <= $budget) {
                return $model;
            }
        }
        return 'gemini-gemini-pro'; // Default
    }
}

$budget = 0.005;
$model = CostOptimizer::selectByBudget($budget);
$service = Arabic::container()->get($model);
```

### 5. Feature-Based Selection

```php
$features = [
    'vision' => ['gemini-gemini-pro-vision', 'openai-gpt-4-vision'],
    'long_context' => ['openai-gpt-4-turbo', 'claude-claude-3-opus'],
    'fast' => ['openai-gpt-3.5-turbo', 'claude-claude-3-haiku'],
    'multilingual' => ['openai-gpt-4', 'gemini-gemini-pro'],
];

// Select model with specific feature
$requiredFeature = 'long_context';
$selectedModel = $features[$requiredFeature][0];
$service = Arabic::container()->get($selectedModel);
```

## Model Characteristics

### Speed Comparison

| Model | Speed | Use Case |
|-------|-------|----------|
| `claude-3-haiku` | ⚡⚡⚡ Fastest | Real-time, chat |
| `gpt-3.5-turbo` | ⚡⚡⚡ Very Fast | Quick tasks |
| `gemini-pro` | ⚡⚡ Fast | General purpose |
| `gpt-4-turbo` | ⚡ Medium | Complex tasks |
| `claude-3-opus` | ⚡ Medium | High quality |

### Cost Comparison (Approximate)

| Model | Cost | Best For |
|-------|------|----------|
| `llama-3.1-8b` | 🆓 FREE | Open source, high volume |
| `mistral-7b` | 🆓 FREE | Open source, fast |
| `command-r` | 🆓 FREE | Free tier, multilingual |
| `deepseek-chat` | 🆓 FREE | Free API, reasoning |
| `claude-3-haiku` | $ | High volume |
| `gemini-pro` | $ | General use |
| `gpt-3.5-turbo` | $$ | Fast tasks |
| `gemini-3-pro` | $$ | Latest Gemini |
| `claude-3-sonnet` | $$$ | Quality tasks |
| `gpt-4-turbo` | $$$ | Long context |
| `claude-3-opus` | $$$$ | High quality |
| `claude-sonnet-4` | $$$$ | New generation |
| `gpt-4` | $$$$ | Critical tasks |
| `claude-sonnet-4.5` | $$$$$ | Latest Claude |
| `chatgpt-5` | $$$$$ | Next-gen OpenAI |
| `chatgpt-5.1` | $$$$$$ | Most advanced |

### Quality Comparison

| Model | Quality | Specialization |
|-------|---------|----------------|
| `chatgpt-5.1` | ⭐⭐⭐⭐⭐⭐ | Most advanced, all tasks |
| `claude-sonnet-4.5` | ⭐⭐⭐⭐⭐⭐ | Latest Claude, reasoning |
| `chatgpt-5` | ⭐⭐⭐⭐⭐ | Next-gen, versatile |
| `claude-sonnet-4` | ⭐⭐⭐⭐⭐ | New generation |
| `claude-3-opus` | ⭐⭐⭐⭐⭐ | Analysis, reasoning |
| `gpt-4` | ⭐⭐⭐⭐⭐ | General purpose |
| `gpt-4-turbo` | ⭐⭐⭐⭐ | Long context |
| `claude-3-sonnet` | ⭐⭐⭐⭐ | Balanced |
| `gemini-3-pro` | ⭐⭐⭐⭐ | Latest Gemini |
| `gemini-pro` | ⭐⭐⭐ | Multilingual |
| `gpt-3.5-turbo` | ⭐⭐⭐ | Fast responses |

## Using Free AI Models

### Why Use Free Models?

- ✅ **No cost** for development and testing
- ✅ **No API keys** required (some models)
- ✅ **Open source** - full control
- ✅ **Privacy** - can run locally
- ✅ **High volume** - no rate limits

### Free Model Setup

```php
// HuggingFace (Free)
Arabic::init([
    new ConfigurableAIModule('huggingface', 'llama-3.1-8b', [
        'api_url' => 'https://api-inference.huggingface.co/models/meta-llama/Llama-3.1-8B',
        'temperature' => 0.7
    ])
]);

// Mistral (Open Source - can run locally)
Arabic::init([
    new ConfigurableAIModule('mistral', 'mistral-7b')
]);

// Cohere (Free Tier)
Arabic::init([
    new ConfigurableAIModule('cohere', 'command-r', [
        'api_key' => 'free-tier-key'  // Get from cohere.com
    ])
]);

// DeepSeek (Free API)
Arabic::init([
    new ConfigurableAIModule('deepseek', 'deepseek-chat')
]);
```

### Free vs Paid Comparison

| Aspect | Free Models | Paid Models |
|--------|-------------|-------------|
| **Cost** | $0 | $0.001 - $0.04 per 1K tokens |
| **Quality** | Good (7B-70B params) | Excellent |
| **Speed** | Medium-Fast | Very Fast |
| **Privacy** | Can run locally | Cloud only |
| **Limits** | May have rate limits | Higher limits |
| **Support** | Community | Official |

### Recommended Free Models for Arabic

```php
// Best free models for Arabic text
$freeArabicModels = [
    'cohere-command-r',           // Best for multilingual (has Arabic)
    'mistral-mistral-7b',         // Fast and capable
    'huggingface-llama-3.1-8b',  // High quality
    'deepseek-deepseek-chat'      // Good reasoning
];
```

## Configuration Options

### Per-Model Configuration

```php
new ConfigurableAIModule('openai', 'gpt-4', [
    'temperature' => 0.8,      // Creativity (0-1)
    'max_tokens' => 2000,      // Max response length
    'top_p' => 0.9,            // Nucleus sampling
    'frequency_penalty' => 0,  // Repetition penalty
    'presence_penalty' => 0,   // Topic diversity
]);
```

### Global Configuration

```php
class AIConfig
{
    public static array $defaults = [
        'gemini' => [
            'temperature' => 0.7,
            'max_tokens' => 1000,
        ],
        'openai' => [
            'temperature' => 0.8,
            'max_tokens' => 2000,
        ],
        'claude' => [
            'temperature' => 0.6,
            'max_tokens' => 4000,
        ],
    ];
}
```

## Advanced Patterns

### 1. Model Pool with Load Balancing

```php
class ModelPool
{
    private array $models;
    private int $currentIndex = 0;
    
    public function __construct(array $models)
    {
        $this->models = $models;
    }
    
    public function getNext(): object
    {
        $model = $this->models[$this->currentIndex];
        $this->currentIndex = ($this->currentIndex + 1) % count($this->models);
        
        return Arabic::container()->get($model);
    }
}

// Use it
$pool = new ModelPool([
    'openai-gpt-4',
    'claude-claude-3-opus',
    'gemini-gemini-pro'
]);

for ($i = 0; $i < 10; $i++) {
    $service = $pool->getNext();
    $result = $service->analyze($text);
}
```

### 2. Adaptive Model Selection

```php
class AdaptiveSelector
{
    private array $performance = [];
    
    public function selectBest(string $task): string
    {
        // Return best performing model for this task
        return $this->performance[$task]['best_model'] 
            ?? 'openai-gpt-4';
    }
    
    public function recordPerformance(
        string $model, 
        string $task, 
        float $score
    ): void {
        if (!isset($this->performance[$task])) {
            $this->performance[$task] = [];
        }
        
        $this->performance[$task][$model] = $score;
        
        // Update best model
        arsort($this->performance[$task]);
        $this->performance[$task]['best_model'] = 
            array_key_first($this->performance[$task]);
    }
}
```

### 3. Fallback Chain

```php
class ModelFallback
{
    private array $chain = [
        'openai-gpt-4',
        'claude-claude-3-opus',
        'gemini-gemini-pro',
        'openai-gpt-3.5-turbo'
    ];
    
    public function execute(string $text): array
    {
        foreach ($this->chain as $model) {
            try {
                $service = Arabic::container()->get($model);
                return $service->analyze($text);
            } catch (Exception $e) {
                continue; // Try next model
            }
        }
        
        throw new Exception('All models failed');
    }
}
```

## Best Practices

### 1. Choose by Use Case

```php
$modelSelection = [
    'translation' => 'openai-gpt-4',
    'summarization' => 'claude-claude-3-sonnet',
    'chat' => 'gpt-3.5-turbo',
    'analysis' => 'claude-claude-3-opus',
    'vision' => 'gemini-gemini-pro-vision',
];

$model = $modelSelection[$useCase];
```

### 2. Cache Model Selection

```php
class ModelCache
{
    private static array $cache = [];
    
    public static function get(string $key): ?object
    {
        return self::$cache[$key] ?? null;
    }
    
    public static function set(string $key, object $service): void
    {
        self::$cache[$key] = $service;
    }
}
```

### 3. Monitor Performance

```php
class ModelMonitor
{
    public static function track(
        string $model, 
        float $duration, 
        int $tokens
    ): void {
        // Log performance metrics
        error_log("Model: {$model}, Duration: {$duration}s, Tokens: {$tokens}");
    }
}
```

## Examples

See [examples/model-selection.php](../examples/model-selection.php) for complete working examples of:
- Direct model selection
- Model comparison
- Smart routing
- Cost optimization
- Feature-based selection

## See Also

- [AI Integration Guide](AI_INTEGRATION.md)
- [Multi-AI Providers](MULTI_AI_PROVIDERS.md)
- [Architecture Documentation](ARCHITECTURE.md)
