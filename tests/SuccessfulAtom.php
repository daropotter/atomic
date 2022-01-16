<?php

namespace Test;

use Atomic\Atom;

class SuccessfulAtom implements Atom
{
    public function __construct(private Counter $counter, private int $increment = 1)
    {
    }

    public function commit(): void
    {
        $this->counter->increment($this->increment);
    }

    public function rollback(): void
    {
        $this->counter->decrement($this->increment);
    }
}
