<?php

namespace App\Concerns;

use Dpb\Departments\Livewire\DepartmentSwitcherComponent;
use Livewire\Attributes\On;

/**
 * Trait for filament pages to handle department picker 
 * change
 */
trait RedirectsOnDepartmentChange
{
    #[On(DepartmentSwitcherComponent::EVENT_DEPARTMENT_CHANGED)]
    public function handleDepartmentChanged(): void
    {
        $this->dispatch('refresh-topbar');
        $this->dispatch('refresh-sidebar');

        if (! $this->canStillAccessPage()) {
            $this->redirect($this->getDepartmentChangeRedirectUrl());

            return;
        }

        $this->handleAccessibleDepartmentChange();
    }

    protected function handleAccessibleDepartmentChange(): void
    {
        $this->dispatch('$refresh');
    }

    protected function getDepartmentChangeRedirectUrl(): string
    {
        return '/';
    }

    abstract protected function canStillAccessPage(): bool;
}
