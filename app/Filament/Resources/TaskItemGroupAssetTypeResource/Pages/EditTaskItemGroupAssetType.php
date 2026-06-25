<?php

namespace App\Filament\Resources\TaskItemGroupAssetTypeResource\Pages;

use App\Filament\Resources\TaskItemGroupAssetTypeResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditTaskItemGroupAssetType extends EditRecord
{
    protected static string $resource = TaskItemGroupAssetTypeResource::class;

    /**
     * asset_type_id is not in TaskItemGroup's $fillable (vendor model), so persist it
     * directly with forceFill to bypass mass-assignment protection.
     */
    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->forceFill([
            'asset_type_id' => $data['asset_type_id'] ?? null,
        ])->save();

        return $record;
    }
}
