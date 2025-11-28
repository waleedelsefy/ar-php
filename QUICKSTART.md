# 🚀 Quick Start Guide

## Installation

```bash
composer require arphp/enhanced
```

## Configuration

### 1. Set Up API Keys

Copy the environment template:
```bash
cp .env.example .env
```

Edit `.env` and add your keys:
```env
GEMINI_API_KEY=your_gemini_key_here
OPENAI_API_KEY=your_openai_key_here
CLAUDE_API_KEY=your_claude_key_here
```

**⚠️ Important:** Add `.env` to `.gitignore` - Never commit API keys!

## Usage

### Minimal Setup (No AI)

```php
use ArPHP\Core\Arabic;

// Initialize core only (smallest footprint)
Arabic::init();

// Use the registry
$registry = Arabic::registry();

// Use the container
$container = Arabic::container();
```

### With Single AI Provider

```php
use ArPHP\Core\Arabic;
use ArPHP\Core\AbstractModule;

// Load only what you need!
Arabic::init([
    new ConfigurableAIModule('gemini', 'gemini-3-pro'),
]);

// Use it
$gemini = Arabic::container()->get('gemini-gemini-3-pro');
$result = $gemini->analyze('النص العربي');
```

### With Multiple AI Providers

```php
// Load multiple models
Arabic::init([
    new ConfigurableAIModule('openai', 'chatgpt-5.1'),
    new ConfigurableAIModule('claude', 'claude-sonnet-4.5'),
    new ConfigurableAIModule('gemini', 'gemini-3-pro'),
]);

// Use any model you loaded
$chatgpt = Arabic::container()->get('openai-chatgpt-5.1');
$claude = Arabic::container()->get('claude-claude-sonnet-4.5');
$gemini = Arabic::container()->get('gemini-gemini-3-pro');
```

### Conditional Loading (Recommended)

```php
$modules = [];

// Load only if API key exists
if (getenv('GEMINI_API_KEY')) {
    $modules[] = new ConfigurableAIModule('gemini', 'gemini-3-pro');
}

if (getenv('OPENAI_API_KEY')) {
    $modules[] = new ConfigurableAIModule('openai', 'chatgpt-5.1');
}

// Always load your custom modules
$modules[] = new MyCustomModule();

Arabic::init($modules);
```

## Available Models

### Latest & Most Capable

| Provider | Model | Best For |
|----------|-------|----------|
| OpenAI | `chatgpt-5.1` | Most advanced, all tasks |
| Claude | `claude-sonnet-4.5` | Latest Claude, reasoning |
| Gemini | `gemini-3-pro` | Latest Gemini |

### Fast & Cost-Effective

| Provider | Model | Best For |
|----------|-------|----------|
| Claude | `claude-3-haiku` | High volume |
| Gemini | `gemini-pro` | General use |
| OpenAI | `gpt-3.5-turbo` | Quick tasks |

### All Available Models

**Gemini:**
- `gemini-pro`, `gemini-3-pro`, `gemini-pro-vision`

**OpenAI:**
- `gpt-3.5-turbo`, `gpt-4`, `gpt-4-turbo`, `chatgpt-5`, `chatgpt-5.1`

**Claude:**
- `claude-3-haiku`, `claude-3-sonnet`, `claude-3-opus`, `claude-sonnet-4`, `claude-sonnet-4.5`

## Examples

Run the examples:
```bash
# Basic usage
php examples/basic-usage.php

# Configuration demo
php examples/configuration.php

# Model selection
php examples/model-selection.php

# Multi-AI providers
php examples/multi-ai-providers.php
```

## Key Principles

✅ **Modular**: Load only what you need  
✅ **Flexible**: Support multiple AI providers  
✅ **Secure**: API keys in .env file  
✅ **Type-Safe**: PHP 8.1+ with strict types  
✅ **Tested**: 49 tests, 100% passing  

## Next Steps

- Read [Getting Started Guide](docs/GETTING_STARTED.md)
- Explore [Model Selection](docs/MODEL_SELECTION.md)
- Check [AI Integration](docs/AI_INTEGRATION.md)
- See [Architecture Documentation](docs/ARCHITECTURE.md)

## Support

- Issues: https://github.com/waleedelsefy/ar-php-enhanced/issues
- Documentation: `/docs` folder

## License

MIT License - Free to use!
