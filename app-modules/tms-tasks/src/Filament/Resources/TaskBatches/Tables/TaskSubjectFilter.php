<?php

namespace Dpb\Modules\Tasks\Filament\Resources\TaskBatches\Tables;

use Dpb\Modules\Tasks\TaskBatches\Services\TaskBatchLookupScopeService;
use Filament\Forms\Components\Select;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;

/**
 * @todo 
 * 
 * does not filter JSON proeprly
 */
class TaskSubjectFilter
{
    public static function make(
        string $filteredColName
    ): Filter {
        return Filter::make($filteredColName)
            ->form([
                Select::make('subject_ids')
                    ->label(__('dpb-mod-tasks::task-batch.table.filters.subject'))
                    ->options(function (
                        TaskBatchLookupScopeService $lookupService,
                    ) {
                        return $lookupService
                            ->taskSubjects()
                            ->mapWithKeys(fn($vehicle) => [
                                $vehicle->vehicle_id => ($vehicle->code
                                    ?? $vehicle->licence_plate
                                    ?? 'N/A')
                            ])
                            ->toArray();
                    })
                    ->searchable()
                    ->multiple()
            ])
            // ->query(function (Builder $query, array $data) use ($filteredColName): Builder {
            //     return $query
            //         ->when(
            //             $data['subject_id'],
            //             fn(Builder $query, $subjectIds): Builder => $query->whereJsonContains('configuration', $subjectIds),
            //         );
            // });
            ->query(function (Builder $query, array $data): Builder {
                // dd($data);
                return $query->when(
                    $data['subject_ids'],
                    function (Builder $query) use ($data) {
                        $query->where(function (Builder $query) use ($data) {
                            foreach ($data['subject_ids'] as $id) {
                                $query->orWhereJsonContains(
                                    'configuration->subjects->vehicle',
                                    (int) $id
                                );
                            }
                        });
                    }
                );
            });
    }
}
