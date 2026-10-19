<?php

namespace Dpb\Modules\Tasks\Eligibility;

use InvalidArgumentException;
use LogicException;

final class SubjectEvaluatorRegistry
{
    /** @var array<string, class-string<SubjectRuleEvaluator>> */
    private array $evaluators = [];

    /**
     * @param class-string<SubjectRuleEvaluator> $evaluatorClass
     */
    public function register(string $key, string $evaluatorClass): void
    {
        if (! is_subclass_of($evaluatorClass, SubjectRuleEvaluator::class)) {
            throw new InvalidArgumentException(
                "Evaluator class [$evaluatorClass] must implement "
                    . SubjectRuleEvaluator::class
            );
        }

        if (isset($this->evaluators[$key])) {
            throw new LogicException(
                "Evaluator [$key] is already registered"
            );
        }

        $this->evaluators[$key] = $evaluatorClass;
    }

    public function resolve(string $key): SubjectRuleEvaluator
    {
        $class = $this->evaluators[$key]
            ?? throw new InvalidArgumentException(
                "Evaluator [$key] is not registered"
            );

        return app($class);
    }
}
