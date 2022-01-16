<?php

namespace Test;

use Atomic\Atom;

class CommitFailingAtom implements Atom
{
    public function __construct(private Counter $counter, private int $increment = 1)
    {
    }

    /** @throws \Exception */
    public function commit(): void
    {
        throw new \Exception("Increment failed");
    }

    public function rollback(): void
    {
        $this->counter->decrement($this->increment);
    }
}
