<?php

namespace Onset;

class Request
{
    private $queryData = [];
    private $postData = [];
    public function __construct(array $query, string $rawJson)
    {
        $this->queryData = $query;

        $rawJson = $rawJson === '' ? '[]' : $rawJson;
        $this->postData = json_decode($rawJson, true);
    }

    public function query(string $key, ?string $default = null): ?string
    {
        return $this->queryData[$key] ?? $default;
    }

    public function value(string $key, ?string $default = null): ?string
    {
        return $this->postData[$key] ?? $default;
    }
}
