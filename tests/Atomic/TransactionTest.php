<?php

declare(strict_types=1);

namespace Test\Atomic;

use Atomic\CommitException;
use Atomic\RollbackException;
use Atomic\Transaction;
use PHPUnit\Framework\TestCase;
use Test\CommitFailingAtom;
use Test\Counter;
use Test\RollbackFailingAtom;
use Test\SuccessfulAtom;

class TransactionTest extends TestCase
{
    private Transaction $transaction;

    protected function setUp(): void
    {
        parent::setUp();
        $this->transaction = new Transaction();
    }

    public function testCommitEmptyTransaction()
    {
        $this->transaction->execute();
        $this->expectNotToPerformAssertions();
    }

    public function testCommitSingleTransaction()
    {
        $counter = new Counter();
        $atom = new SuccessfulAtom($counter);

        $this->transaction->append($atom);
        $this->assertEquals(0, $counter->getValue());

        $this->transaction->execute();
        $this->assertEquals(1, $counter->getValue());
    }

    public function testCommitMultiTransaction()
    {
        $counter = new Counter();
        $atom1 = new SuccessfulAtom($counter, 1);
        $atom2 = new SuccessfulAtom($counter, 2);

        $this->transaction->append($atom1);
        $this->transaction->append($atom2);
        $this->assertEquals(0, $counter->getValue());

        $this->transaction->execute();
        $this->assertEquals(3, $counter->getValue());
    }

    public function testFailingSingleTransaction()
    {
        $counter = new Counter();
        $atom = new CommitFailingAtom($counter);

        $this->transaction->append($atom);
        $this->assertEquals(0, $counter->getValue());

        $this->expectException(CommitException::class);
        try {
            $this->transaction->execute();
        } catch (\Exception $e) {
            $this->assertEquals(0, $counter->getValue());
            throw $e;
        }
    }

    public function testCommitFailingMultiTransaction()
    {
        $counter = new Counter();
        $atom1 = new SuccessfulAtom($counter, 1);
        $atom2 = new CommitFailingAtom($counter, 2);

        $this->transaction->append($atom1);
        $this->transaction->append($atom2);
        $this->assertEquals(0, $counter->getValue());

        $this->expectException(CommitException::class);
        try {
            $this->transaction->execute();
        } catch (\Exception $e) {
            $this->assertEquals(0, $counter->getValue());
            throw $e;
        }
    }

    public function testRollbackFailingMultiTransaction()
    {
        $counter = new Counter();
        $atom1 = new SuccessfulAtom($counter, 1);
        $atom2 = new RollbackFailingAtom($counter, 2);
        $atom3 = new CommitFailingAtom($counter, 4);

        $this->transaction->append($atom2);
        $this->transaction->append($atom3);
        $this->transaction->prepend($atom1);
        $this->assertEquals(0, $counter->getValue());

        $this->expectException(RollbackException::class);
        try {
            $this->transaction->execute();
        } catch (\Exception $e) {
            $this->assertEquals(3, $counter->getValue());
            throw $e;
        }
    }
}
