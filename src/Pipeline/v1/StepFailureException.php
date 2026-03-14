<?php declare(strict_types=1);

namespace Coco\SourceWatcherApi\Pipeline\v1;

/**
 * Thrown when a pipeline step fails. Carries step index and name for error reporting.
 */
class StepFailureException extends \RuntimeException
{
    public function __construct(
        string $message,
        private readonly int $stepIndex,
        private readonly string $stepName,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function getStepIndex(): int
    {
        return $this->stepIndex;
    }

    public function getStepName(): string
    {
        return $this->stepName;
    }
}
