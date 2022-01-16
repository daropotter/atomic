<?php

namespace Test;

use Atomic\Atom;

class RollbackFailingAtom implements Atom
{
    public function __construct(private Counter $counter, private int $increment = 1)
    {
    }

    public function commit(): void
    {
        $this->counter->increment($this->increment);
    }

    /** @throws \Exception */
    public function rollback(): void
    {
        throw new \Exception("Decrement failed");
    }
}
