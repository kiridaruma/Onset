<?php

namespace Onset;

class App
{
    private $configs;
    public function __construct(array $config)
    {
        $this->configs = $config;
    }

    public function config(): array
    {
        return $this->configs;
    }

    private $container;
    public function bind(string $key, callable $f): void
    {
        $this->container[$key] = $f;
    }

    public function resolve(string $key)
    {
        $f = $this->container[$key] ?? null;
        if ($f === null) {
            throw new \OutOfBoundsException("\"{$key}\" was not binded.");
        }
        return $f($this);
    }
}
