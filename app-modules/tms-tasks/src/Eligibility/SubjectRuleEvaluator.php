<?php

namespace Dpb\Modules\Tasks\Eligibility;

use Illuminate\Database\Eloquent\Model;

interface SubjectRuleEvaluator
{
    public function supports(Model $subject): bool;

    public function matches(
        Model $subject,
        array $conditions,
    ): bool;
}