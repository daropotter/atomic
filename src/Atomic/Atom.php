<?php

namespace Atomic;

interface Atom
{
    public function commit(): void;

    public function rollback(): void;
}
