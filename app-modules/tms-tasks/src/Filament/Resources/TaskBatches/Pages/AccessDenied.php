<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Pages;

use Filament\Pages\Page;

class AccessDenied extends Page
{
    protected string $view = 'dpb-mod-tasks::filament.pages.access-denied';

    public string $message = 'You do not have permission to access this resource.';

    public string $returnUrl;

    public function mount(): void
    {
        $this->returnUrl = route(
            'filament.admin.resources.task-batches.index'
        );
    }
}