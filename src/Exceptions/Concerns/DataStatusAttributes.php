<?php

namespace Biin2013\Tiger\Exceptions\Concerns;

trait DataStatusAttributes
{
    private int $status;
    /**
     * @var array<string, mixed>
     */
    private array $data = [];

    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @return array<string, mixed>
     */
    public function getData(): array
    {
        return $this->data;
    }
}