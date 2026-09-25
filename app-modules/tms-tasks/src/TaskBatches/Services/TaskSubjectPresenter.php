<?php

namespace Dpb\Modules\Tasks\TaskBatches\Services;

use Dpb\Package\Fleet\Models\Vehicle;
use Illuminate\Database\Eloquent\Model;

final class TaskSubjectPresenter
{
    public function __construct(
        private readonly ?Model $subject,
    ) {}

    public function id(): int|string|null
    {
        return $this->subject?->getKey();
    }

    public function code(): ?string
    {
        return match (true) {
            $this->subject instanceof Vehicle => $this->subject->label,
            // $this->subject instanceof Building => $this->subject->building_code,
            default => null,
        };
    }

    public function label(): ?string
    {
        return match (true) {
            $this->subject instanceof Vehicle => $this->subject->label,
            // $this->subject instanceof Building => $this->subject->address,
            default => null,
        };
    }

    public function description(): ?string
    {
        return match (true) {
            $this->subject instanceof Vehicle => $this->subject->model->title,
            // $this->subject instanceof Building => $this->subject->address,
            default => null,
        };
    }    

    public function size(): ?string
    {
        return match (true) {
            $this->subject instanceof Vehicle => $this->subject->model->length,
            // $this->subject instanceof Building => $this->subject->address,
            default => null,
        };
    }    
    
    public function seats(): ?string
    {
        return match (true) {
            $this->subject instanceof Vehicle => $this->subject->model->seats,
            // $this->subject instanceof Building => $this->subject->address,
            default => null,
        };
    }        
}
