<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Actions;

use Dpb\Package\Tasks\Models\TaskItem;
use Dpb\Modules\Tasks\Filament\Pages\TaskBatchWorkOrdersPage;
use Dpb\Modules\Tasks\Helpers\Base64UrlHelper;
use Dpb\Modules\Tasks\Models\TaskBatch;
use Filament\Actions\Action;
use Illuminate\Support\Facades\DB;

class AssignWorkAction
{
    public static function make(?string $name = 'assign_work_action'): Action
    {
        return Action::make($name)
            ->url(function ($record) {
                /** @var TaskBatch $record */
                $taskItemMorph = app(TaskItem::class)->getMorphClass();

                $taskItemIds = DB::table('dpb_batchable_batch_records')
                    ->where('batch_id', $record->batch_id)
                    ->where('record_type', $taskItemMorph)
                    ->pluck('record_id');

                $taskItemIdsData = Base64UrlHelper::encode(
                    json_encode($taskItemIds->values()->all())
                );

                return TaskBatchWorkOrdersPage::getUrl([
                    'taskItems' => $taskItemIdsData,
                    'batchId' => $record->batch->id,
                    'taskBatchId' => $record->id,
                ]);
            })
            ->icon('heroicon-o-wrench');
    }
}
