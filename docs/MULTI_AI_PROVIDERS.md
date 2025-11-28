# Multi-AI Provider Integration

## Overview

This guide shows how to use multiple AI providers (Gemini, OpenAI, Claude) simultaneously in ArPHP Enhanced.

## Architecture

```
┌─────────────────────────────────────────────┐
│         AIAggregator Module                 │
│  (Combines all AI providers)                │
└─────────────────────────────────────────────┘
         ↓          ↓          ↓
┌─────────────┐ ┌─────────────┐ ┌─────────────┐
│   Gemini    │ │   OpenAI    │ │   Claude    │
│   Module    │ │   Module    │ │   Module    │
└─────────────┘ └─────────────┘ └─────────────┘
```

## Installation

```bash
# Install AI provider packages
composer require google/generative-ai-php
composer require openai-php/client
composer require anthropic-php/client
```

## Configuration

Create a `.env` file:

```env
GEMINI_API_KEY=your_gemini_key_here
OPENAI_API_KEY=your_openai_key_here
CLAUDE_API_KEY=your_claude_key_here
```

## Usage Examples

### 1. Initialize All Providers

```php
use ArPHP\Core\Arabic;

Arabic::init([
    new GeminiModule(),
    new OpenAIModule(),
    new ClaudeModule(),
    new AIAggregatorModule(),  // Combines all
]);
```

### 2. Use Individual Provider

```php
// Use Gemini
$gemini = Arabic::container()->get('gemini');
$result = $gemini->analyze('النص العربي');

// Use OpenAI
$openai = Arabic::container()->get('openai');
$result = $openai->analyze('النص العربي');

// Use Claude
$claude = Arabic::container()->get('claude');
$result = $claude->analyze('النص العربي');
```

### 3. Use Aggregator (All at Once)

```php
// Get aggregator
$aggregator = Arabic::container()->get('ai-aggregator');

// Analyze with all providers
$results = $aggregator->analyzeWithAll('النص العربي');

// Results from all 3 providers
foreach ($results as $provider => $result) {
    echo "{$provider}: {$result['sentiment']} ({$result['confidence']})\n";
}
```

### 4. Get Best Result

```php
$aggregator = Arabic::container()->get('ai-aggregator');

// Analyze with all
$results = $aggregator->analyzeWithAll($text);

// Get best result (highest confidence)
$best = $aggregator->getBestResult($results);

echo "Best provider: {$best['provider']}\n";
echo "Confidence: {$best['confidence']}\n";
```

### 5. Get Consensus

```php
$aggregator = Arabic::container()->get('ai-aggregator');

// Analyze with all
$results = $aggregator->analyzeWithAll($text);

// Get consensus from all providers
$consensus = $aggregator->getConsensus($results);

print_r($consensus);
/*
Array (
    [consensus_sentiment] => positive
    [average_confidence] => 0.92
    [agreement] => 1  // 100% agreement
    [providers_count] => 3
)
*/
```

### 6. Fallback Strategy

```php
function analyzeWithFallback(string $text): array
{
    $providers = ['gemini', 'openai', 'claude'];
    
    foreach ($providers as $provider) {
        try {
            $service = Arabic::container()->get($provider);
            return $service->analyze($text);
        } catch (Exception $e) {
            continue;  // Try next provider
        }
    }
    
    throw new Exception("All providers failed");
}

$result = analyzeWithFallback($arabicText);
```

## Use Cases

### 1. Consensus Analysis
Use multiple AI providers and take the majority vote:

```php
$results = $aggregator->analyzeWithAll($text);
$consensus = $aggregator->getConsensus($results);

if ($consensus['agreement'] >= 0.67) {  // 2 out of 3 agree
    echo "High confidence: {$consensus['consensus_sentiment']}";
}
```

### 2. Quality Check
Compare results from different providers:

```php
$results = $aggregator->analyzeWithAll($text);

$confidences = array_column($results, 'confidence');
$avgConfidence = array_sum($confidences) / count($confidences);

if ($avgConfidence > 0.9) {
    echo "High quality analysis";
}
```

### 3. Cost Optimization
Use cheaper provider first, expensive as backup:

```php
// Try Gemini (cheaper)
try {
    $result = Arabic::container()->get('gemini')->analyze($text);
} catch (Exception $e) {
    // Fallback to GPT-4 (more expensive but reliable)
    $result = Arabic::container()->get('openai')->analyze($text);
}
```

### 4. A/B Testing
Compare performance between providers:

```php
$results = $aggregator->analyzeWithAll($text);

foreach ($results as $provider => $result) {
    // Log for comparison
    logPerformance($provider, $result['confidence'], $result['time']);
}
```

## Advanced: Custom Aggregation Logic

```php
class CustomAIAggregator extends AIAggregator
{
    public function getWeightedConsensus(array $results): array
    {
        // Give different weights to different providers
        $weights = [
            'gemini' => 0.3,
            'openai' => 0.4,
            'claude' => 0.3,
        ];
        
        $weightedScore = 0;
        foreach ($results as $provider => $result) {
            $score = $result['confidence'] * $weights[$provider];
            $weightedScore += $score;
        }
        
        return [
            'weighted_score' => $weightedScore,
            'providers' => array_keys($results),
        ];
    }
}
```

## Module Dependencies

The `AIAggregatorModule` automatically declares dependencies:

```php
class AIAggregatorModule extends AbstractModule
{
    // These will be loaded first automatically
    protected array $dependencies = ['gemini', 'openai', 'claude'];
    
    // Rest of the code...
}
```

This ensures all AI providers are initialized before the aggregator.

## Benefits

| Feature | Benefit |
|---------|---------|
| **Redundancy** | If one provider fails, others continue |
| **Consensus** | More reliable results from multiple sources |
| **Comparison** | Compare quality across providers |
| **Flexibility** | Easy to add/remove providers |
| **Cost Control** | Use cheaper providers as primary |

## Performance Tips

1. **Cache Results**: Don't analyze same text multiple times
2. **Parallel Requests**: Use async/parallel HTTP requests
3. **Rate Limiting**: Respect API rate limits
4. **Fallback Chain**: Order by cost/speed
5. **Selective Analysis**: Only use multiple providers when needed

## Error Handling

```php
try {
    $results = $aggregator->analyzeWithAll($text);
} catch (Exception $e) {
    // Handle if all providers fail
    $fallbackResult = useLocalModel($text);
}
```

## See Also

- [AI Integration Guide](AI_INTEGRATION.md)
- [Example: Multi-AI Providers](../examples/multi-ai-providers.php)
- [Architecture Documentation](ARCHITECTURE.md)
