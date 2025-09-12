<?php

namespace App\Models\Inspection;

use Dpb\Packages\Inspections\Models\Inspection as BaseInspection;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Inspection extends BaseInspection
{
    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->mergeFillable(['subject_id', 'subject_type']);
    }

    public function subject(): MorphTo
    {
        return $this->morphTo();
    }
}
