<?php

declare(strict_types=1);

namespace Atomic;

class Transaction
{
    /** @var Atom[] */
    private array $atoms = [];

    public function append(Atom $atom): void
    {
        $this->atoms[] = $atom;
    }

    public function prepend(Atom $atom): void
    {
        array_unshift($this->atoms, $atom);
    }

    /** @throws CommitException|RollbackException */
    public function execute(): void
    {
        for ($i = 0; $i < count($this->atoms); $i++) {
            $atom = $this->atoms[$i];
            try {
                $atom->commit();
            } catch (\Exception $e) {
                $this->rollbackAtoms($i);
                throw new CommitException(
                    "Atomic transaction commit failed at position $i. Previous atoms were rolled back.",
                    0,
                    $e
                );
            }
        }
    }

    /**
     * @throws RollbackException
     */
    private function rollbackAtoms(int $failedIndex)
    {
        for ($i = $failedIndex - 1; $i >= 0; $i--) {
            $atom = $this->atoms[$i];
            try {
                $atom->rollback();
            } catch (\Exception $e) {
                throw new RollbackException(
                    "Atomic transaction commit failed at position $failedIndex. " .
                        "Rollback failed also at position $i.",
                    0,
                    $e
                );
            }
        }
    }
}
