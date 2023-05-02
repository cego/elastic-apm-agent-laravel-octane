<?php

namespace Cego\ElasticApmAgentLaravelOctane;

use BadMethodCallException;
use Elastic\Apm\ElasticApm;
use Elastic\Apm\ExecutionSegmentInterface;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Elastic\Apm\SpanInterface;
use Elastic\Apm\TransactionInterface;
use Laravel\Octane\Swoole\WorkerState;

class OctaneApmManager
{
    /**
     * Dictates if the current request should be sampled.
     *
     * @var bool
     */
    private bool $disabled;

    /**
     * The main outer transaction wrapping all child spans.
     *
     * @var TransactionInterface
     */
    private TransactionInterface $transaction;

    /**
     * Holds all stored spans indexed by their name
     *
     * @var array<string, SpanInterface>
     */
    private array $spans = [];

    private static $knownTransactionIds = [];

    private static $knownTraceIds = [];

    public int $transactionCount = 0;

    /**
     * Constructor
     */
    public function __construct()
    {
        // Randomly disable the manager so only some requests are sampled
        $this->disabled = !class_exists(ElasticApm::class);
    }

    /**
     * Begins a new transaction.
     *
     * Will discard any active transactions.
     *
     * @param string $name
     * @param string $type
     *
     * @return TransactionInterface|null
     */
    public function beginTransaction(string $name, string $type): ?TransactionInterface
    {
        if ($this->disabled) {
            return null;
        }

        $this->prepareForNextTransaction();

        $this->transactionCount++;

        $this->transaction = ElasticApm::newTransaction($name, $type)
            ->distributedTracingHeaderExtractor(fn() => Str::random(32))
            ->asCurrent()
            ->begin();

        if (in_array($this->transaction->getId(), static::$knownTransactionIds)) {
            $this->log('DUPLICATED TRANSACTION ID');
        } else {
            static::$knownTransactionIds[] = $this->transaction->getId();
        }

        if (in_array($this->transaction->getTraceId(), static::$knownTraceIds)) {
            $this->log('DUPLICATED TRACE ID');
        } else {
            static::$knownTraceIds[] = $this->transaction->getTraceId();
        }

        if ($this->transaction->getParentId() !== null) {
            $this->log('Transaction has parent id?!');
        }

        $this->log(__METHOD__);

        return $this->transaction;
    }

    /**
     * Prepares the manager and APM for the next request
     *
     * @return void
     */
    private function prepareForNextTransaction(): void
    {
        $this->log(__METHOD__);

        // If there is a hanging transaction, then discard it.
        $this->discardActiveSegments();
        $this->resetManager();
    }

    /**
     * Discards all currently active APM segments
     *
     * @return void
     */
    private function discardActiveSegments(): void
    {
        $this->log(__METHOD__);

        $this->discardSegment(ElasticApm::getCurrentTransaction());
        $this->discardSegment(ElasticApm::getCurrentExecutionSegment());

        if (isset($this->transaction)) {
            $this->discardSegment($this->transaction);
        }

        foreach ($this->spans as $span) {
            $this->discardSegment($span);
        }
    }

    /**
     * Resets the manager state, so all stored variables are cleared.
     *
     * @return void
     */
    private function resetManager(): void
    {
        unset($this->transaction);
        $this->spans = [];

        Log::withoutContext();
    }

    /**
     * Begins a new span
     *
     * @param string $name
     * @param string $type
     *
     * @return SpanInterface|null
     */
    public function beginAndStoreSpan(string $name, string $type): ?SpanInterface
    {
        $this->log(__METHOD__);

        if ($this->disabled) {
            return null;
        }

        if ($this->hasNoTransactionInstance()) {
            throw new BadMethodCallException('Cannot start span without first starting a transaction');
        }

        if (isset($this->spans[$name])) {
            throw new InvalidArgumentException('Nested stored spans with the same name is not supported');
        }

        return $this->spans[$name] = $this->transaction->beginChildSpan($name, $type);
    }

    /**
     * Ends a stored span
     *
     * @param string $name
     *
     * @return void
     */
    public function endStoredSpan(string $name): void
    {
        $this->log(__METHOD__);

        if ($this->disabled) {
            return;
        }

        if (!isset($this->spans[$name])) {
            throw new InvalidArgumentException('No stored span with name [%s] exists');
        }

        $this->endSegment($this->spans[$name]);
        unset($this->spans[$name]);
    }

    /**
     * Returns the current transaction
     *
     * @return TransactionInterface|null
     */
    public function getTransaction(): ?TransactionInterface
    {
        if ($this->disabled) {
            return null;
        }

        return $this->transaction ?? null;
    }

    /**
     * Set the result of the transaction
     *
     * @param string|null $result
     * @return void
     */
    public function setTransactionResult(?string $result): void
    {
        $this->log(__METHOD__);

        $this->getTransaction()?->setResult($result);
    }

    /**
     * Returns true if there exists a transaction instance within the manager
     *
     * @return bool
     */
    public function hasNoTransactionInstance(): bool
    {
        $this->log(__METHOD__);

        if ($this->disabled) {
            return true;
        }

        return !isset($this->transaction);
    }

    /**
     * Ends the transaction
     *
     * @return void
     */
    public function endTransaction(): void
    {
        $this->log(__METHOD__);

        if ($this->disabled) {
            return;
        }

        if ($this->hasNoTransactionInstance()) {
            throw new BadMethodCallException('Cannot start transaction before it has been started');
        }

        foreach (array_keys($this->spans) as $spanKey) {
            $this->endStoredSpan($spanKey);
        }

        $this->endSegment($this->transaction);
        $this->resetManager();
    }

    /**
     * Discards the given execution segment
     *
     * @param ExecutionSegmentInterface $segment
     * @return void
     */
    private function discardSegment(ExecutionSegmentInterface $segment): void
    {
        $this->log(__METHOD__);

        if ($segment->hasEnded()) {
            return;
        }

        $segment->discard();
    }

    /**
     * Ends the given execution segment
     *
     * @param ExecutionSegmentInterface $segment
     * @return void
     */
    private function endSegment(ExecutionSegmentInterface $segment): void
    {
        $this->log(__METHOD__);

        if ($segment->hasEnded()) {
            return;
        }

        $segment->end();
    }

    private function log(string $message): void
    {
        $workerState = resolve(WorkerState::class);

        Log::info($message, [
            'transaction' => [
                'id'        => $this->getTransaction()?->getId(),
                'trace'     => $this->getTransaction()?->getTraceId(),
                'parent_id' => $this->getTransaction()?->getParentId(),
                'count'     => $this->transactionCount,
            ],
            'process'     => [
                'pid' => getmypid(),
                'gid' => getmygid(),
            ],
            'worker'      => [
                'id'            => $workerState->workerId,
                'pid'           => $workerState->workerPid,
                'spl_object_id' => spl_object_id($workerState->worker),
            ]
        ]);
    }
}
