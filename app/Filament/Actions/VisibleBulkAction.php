<?php

namespace App\Filament\Actions;

use Filament\Actions\BulkAction;

class VisibleBulkAction extends BulkAction
{
    public function getExtraAttributes(): array
    {
        return [
            ...parent::getExtraAttributes(),
            'x-cloak' => true,
            'x-show' => true,
            ':disabled' => 'getSelectedRecordsCount() === 0',
        ];
    }
}