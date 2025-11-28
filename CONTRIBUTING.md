# Contributing to ArPHP Enhanced

Thank you for your interest in contributing to ArPHP Enhanced! This document provides guidelines and steps for contributing.

## Code of Conduct

By participating in this project, you agree to maintain a respectful and inclusive environment for everyone.

## How to Contribute

### Reporting Bugs

1. Check if the bug has already been reported in [Issues](https://github.com/waleedelsefy/ar-php-enhanced/issues)
2. If not, create a new issue with:
   - Clear, descriptive title
   - Steps to reproduce
   - Expected behavior
   - Actual behavior
   - PHP version and environment details

### Suggesting Features

1. Check existing issues for similar suggestions
2. Create a new issue with:
   - Clear description of the feature
   - Use cases and benefits
   - Possible implementation approach

### Pull Requests

1. Fork the repository
2. Create a feature branch: `git checkout -b feature/my-feature`
3. Make your changes
4. Run tests: `composer test`
5. Run static analysis: `composer phpstan`
6. Commit with descriptive message
7. Push to your fork
8. Create a Pull Request

## Development Setup

```bash
# Clone your fork
git clone https://github.com/YOUR_USERNAME/ar-php-enhanced.git
cd ar-php-enhanced

# Install dependencies
composer install

# Run tests
composer test

# Run static analysis
composer phpstan
```

## Coding Standards

- Follow PSR-12 coding style
- Use strict types: `declare(strict_types=1);`
- Add PHPDoc blocks for all public methods
- Write tests for new features
- Maintain PHPStan level 8 compliance

## Creating Modules

When creating new modules:

1. Implement `ModuleInterface` or extend `AbstractModule`
2. Add comprehensive tests
3. Update documentation
4. Add usage examples

## Questions?

Feel free to open an issue for any questions about contributing.
