<?php

namespace App\Registries\Snapshots;

class TaskItemSnapshotSQLRegistry
{
    protected function targetTable(): string
    {
        return 'mvw_task_item_snapshots';
    }

    public function build(): string
    {
        return "
            INSERT INTO {$this->targetTable()}
            ({$this->columns()})
            SELECT
                {$this->select()}
            {$this->from()}
            {$this->joins()}
            ON DUPLICATE KEY UPDATE
                updated_at = VALUES(updated_at)            
        ";
    }

    protected function map(): array
    {
        return [
            'task_item_id' => 'task_item_id',
            'task_id' => 't.id',
            'task_date' => 't.date',
            'task_title' => 't.title',
            'task_description' => 't.description',
            'task_group_title' => 'tg.title',

            'task_assigned_to_type' => 'NULL',
            'task_assigned_to_label' => 'NULL',
            'task_requested_for_type' => 'NULL',
            'task_requested_for_label' => 'NULL',

            'task_author_lastname' => 'tu.lastname',
            'task_place_of_origin' => 'po.title',
            'task_created_at' => 't.created_at',

            'task_item_date' => 'ti.date',
            'task_item_title' => 'ti.title',
            'task_item_description' => 'ti.description',
            'task_item_group_title' => 'tig.title',

            'task_item_assigned_to_type' => 'NULL',
            'task_item_assigned_to_label' => 'NULL',

            'task_item_author_lastname' => 'tiu.lastname',
            'task_item_created_at' => 'tia.created_at',

            'updated_at' => 'tia.updated_at',
        ];
    }

    protected function columns(): string
    {
        return implode(',', array_keys($this->map()));
    }

    protected function select(): string
    {
        return implode(",\n", $this->map());
    }

    protected function joins(): string
    {
        return "
            JOIN tmp_task_item_ids tmp ON tmp.id = tia.id
            LEFT JOIN tsk_task_items ti ON tia.task_item_id = ti.id
            LEFT JOIN tsk_task_item_groups tig ON tig.id = ti.group_id
            LEFT JOIN users tiu ON tiu.id = tia.author_id
            LEFT JOIN tms_task_assignments ta ON ta.task_id = ti.task_id
            LEFT JOIN tsk_tasks t ON t.id = ta.task_id
            LEFT JOIN tsk_task_groups tg ON tg.id = t.group_id
            LEFT JOIN users tu ON tu.id = ta.author_id
            LEFT JOIN tsk_places_of_origin po ON po.id = t.place_of_origin_id
        ";
    }

    protected function from(): string
    {
        return "
            FROM tms_task_item_assignments tia
        ";
    }

    public function polymorphicContext(array $taskItemIds = []): string
    {
        $base = "
            SELECT
                tia.task_item_id as task_item_id,
                tia.assigned_to_id as task_item_assigned_to_id,
                tia.assigned_to_type as task_item_assigned_to_type,

                ta.assigned_to_id as task_assigned_to_id,
                ta.assigned_to_type as task_assigned_to_type,
                ta.subject_id as task_subject_id,
                ta.subject_type as task_subject_type,

                ta.department_id as task_requested_for_id,
                CASE
                    WHEN ta.department_id IS NOT NULL THEN 'department'
                    ELSE NULL
                END AS task_requested_for_type

            FROM tms_task_item_assignments tia
            LEFT JOIN tsk_task_items ti ON ti.id = tia.task_item_id
            LEFT JOIN tms_task_assignments ta ON ta.task_id = ti.task_id
        ";

        if (!empty($taskItemIds)) {
            $base .= " WHERE tia.task_item_id IN (" . implode(',', $taskItemIds) . ")";
        }

        return $base;
    }    
}
