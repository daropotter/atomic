<?php

namespace Test;

class Counter
{
    public function __construct(private int $value = 0)
    {
    }

    public function increment(int $increment = 1): void
    {
        $this->value += $increment;
    }

    public function decrement(int $decrement = 1): void
    {
        $this->value -= $decrement;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function setValue(int $value): void
    {
        $this->value = $value;
    }
}
