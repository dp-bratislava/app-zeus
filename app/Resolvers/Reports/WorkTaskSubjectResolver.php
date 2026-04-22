<?php

namespace App\Resolvers\Reports;

use Dpb\WorkTimeFund\Models\Maintainables\Table;
use Dpb\WorkTimeFund\Models\Maintainables\Vehicle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class WorkTaskSubjectResolver
{
    private $vehicles = null;
    private $tables = null;
    private $attributes = null;

    protected array $map = [
        'Dpb\WorkTimeFund\Models\Maintainables\Vehicle' => 'resolveVehicle',
        'Dpb\WorkTimeFund\Models\Maintainables\Table' => 'resolveTable',
    ];

    public function batchResolve($activities): ?array
    {
        $result = [];

        if (empty($activities)) {
            return $result;
        }

        // vehicles
        $vehicleIds = $activities->where('maintainable_type', Vehicle::class)->pluck('maintainable_id');
        $this->vehicles = Vehicle::whereIn('id', $vehicleIds)
            ->with(['codes', 'licencePlates']) // ONLY what resolver needs
            ->get()
            ->keyBy('id');

        // tables
        $tableIds = $activities->where('maintainable_type', Table::class)->pluck('maintainable_id');
        $this->tables = Table::whereIn('id', $tableIds)
            ->with(['locations']) // ONLY what resolver needs
            ->get()
            ->keyBy('id');

        // custom
        // $taskIds = $activities->whereNull('maintainable_type')->pluck('task_id');
        // $this->attributes = DB::table('dpb_worktimefund_model_attributeoption as ato')
        //     ->leftJoin('dpb_worktimefund_model_attributetype as att', 'att.id', '=', 'ato.attributetype_id')
        //     ->leftJoin('dpb_worktimefund_mm_morphable_attributeoption as mato', 'mato.attributeoption_id', '=', 'ato.id')
        //     // ->leftJoin('dpb_worktimefund_model_task as wt', 'wt.id', '=', 'mato.morphable_id')
        //     ->select([
        //         'att.title as subject_type',
        //         'ato.label as subject_label',
        //         't.maintainable_type',
        //         't.maintainable_id',
        //     ])
        //     ->where('mato.morphable_type', '=', 'Dpb\WorkTimeFund\Models\Task')
        //     ->whereIn('mato.morphable_id', $taskIds)
        //     ->get()
        //     ->keyBy('id');

        foreach ($activities as $activity) {
            $subject = $this->resolve($activity);
            if ($subject !== null) {
                $subject['activity_id'] = $activity->id;
                $result[] = $subject;
            }

        }

        return $result;
    }

    public function resolve($activity): ?array
    {
        if (!$activity->maintainable_type) {
            return null;
        }

        if (isset($this->map[$activity->maintainable_type])) {
            return $this->{$this->map[$activity->maintainable_type]}($activity->maintainable_id);
        }

        return null;
        // cutom from attribute attributeOptions
        // return $this->resolveCustom($model);
    }

    public function resolve1(Model|null $model): ?array
    {
        if (!$model) {
            return null;
        }

        if ($model?->maintainable !== null) {
            $class = get_class($model->maintainable);
            if (isset($this->map[$class])) {
                return $this->{$this->map[$class]}($model->maintainable);
            }
        }

        // cutom from attribute attributeOptions
        return $this->resolveCustom($model);
    }

    protected function resolveVehicle($id): ?array
    {
        return [
            'subject_type' => 'Vozidlo',
            // 'label' => $model->getTitle()
            'subject_label' => $this->vehicles[$id]->getTitle()
            // 'subject_label' => $id
        ];
    }

    protected function resolveTable($id): ?array
    {
        return [
            'subject_type' => 'Tabuľa',
            // 'label' => $model->getTitle()
            'subject_label' => $this->tables[$id]->getTitle()
        ];
    }

    private function resolveCustom($model): ?array
    {
        if (empty($model->attributeOptions)) {
            return null;
        }

        // Task
        $types = [];
        $labels = [];
        foreach ($model->attributeOptions as $attribute) {
            $types[] = $attribute->type->title;
            $labels[] = $attribute->label;
        }

        return [
            'type' => join(', ', $types),
            'label' => join(', ', $labels),
        ];
        /*
        $attribute = $model->attributes->first();
        // dd(
        //     $attribute->label,
        //     // $attribute->type->title
        // );
        return [
            'subject_type' => $attribute->type->title,
            'subject_id' => $attribute->id,
            'label' => $attribute->label,
            'code' => null,
            'meta' => json_encode([], JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ];
        */
    }
}
