<?php

declare(strict_types=1);

namespace ArPHP\Core;

use ArPHP\Core\Contracts\ModuleInterface;

/**
 * Abstract base class for creating modules
 *
 * @package ArPHP\Core
 * @author Waleed Elsefyy
 */
abstract class AbstractModule implements ModuleInterface
{
    protected bool $enabled = true;
    /** @var array<string> */
    protected array $dependencies = [];
    protected string $version = '1.0.0';

    abstract public function getName(): string;

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getDependencies(): array
    {
        return $this->dependencies;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function enable(): void
    {
        $this->enabled = true;
    }

    public function disable(): void
    {
        $this->enabled = false;
    }

    public function register(): void
    {
        // Override in child class
    }

    public function boot(): void
    {
        // Override in child class if needed
    }
}

