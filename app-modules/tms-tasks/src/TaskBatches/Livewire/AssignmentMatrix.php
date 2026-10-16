<?php

namespace Dpb\Modules\Tasks\TaskBatches\Livewire;

use Livewire\Component;

class AssignmentMatrix extends Component
{
    public array $subjects = [];

    public array $groups = [];

    /**
     * Assignment state:
     *
     * [
     *     subjectId => [
     *         groupId => [
     *             'work' => [
     *                 ['contract_id' => 42, 'quantity' => 1],
     *             ],
     *         ],
     *     ],
     * ]
     */
    public array $matrix = [];
    public array $notes = [];

    public array $contractInputs = [];
    public array $contractOptions = [];

    public array $contractSearch = [];

    public array $quantityInputs = [];

    public function mount(
        array $subjects = [],
        array $groups = [],
        array $state = [],
        array $contractOptions = [],
    ): void {
        $this->subjects = $subjects;
        $this->groups = $groups;
        $this->contractOptions = $contractOptions;
        $this->matrix = $state;

        // Normalize work collections to use contract_id as the key.
        foreach ($this->matrix as $subjectId => $subjectGroups) {
            foreach ($subjectGroups as $groupId => $cell) {
                $normalizedWork = [];

                foreach ($cell['work'] ?? [] as $work) {
                    if (! is_array($work) || ! isset($work['contract_id'])) {
                        continue;
                    }

                    $contractId = (int) $work['contract_id'];

                    $normalizedWork[$contractId] = [
                        'contract_id' => $contractId,
                        'quantity' => (int) ($work['quantity'] ?? 1),
                    ];
                }

                $this->matrix[$subjectId][$groupId]['work'] = $normalizedWork;
            }
        }

        foreach ($this->subjects as $subject) {
            $subjectId = (int) $subject['id'];

            $this->notes[$subjectId] ??= '';

            foreach ($this->groups as $group) {
                $groupId = (int) $group['id'];

                $this->matrix[$subjectId][$groupId] ??= [
                    'work' => [],
                ];

                $this->matrix[$subjectId][$groupId]['work'] ??= [];
                $this->contractInputs[$subjectId][$groupId] ??= '';
                $this->quantityInputs[$subjectId][$groupId] ??= 1;
            }
        }
    }

    public function updatedMatrix(): void
    {
        $this->notifyParent();
    }

    public function updatedNotes(): void
    {
        $this->notifyParent();
    }

    public function selectContract(
        int $subjectId,
        int $groupId,
        int $contractId,
    ): void {
        abort_unless($this->hasSubject($subjectId), 404);
        abort_unless($this->hasGroup($groupId), 404);
        abort_unless(
            array_key_exists($contractId, $this->contractOptions),
            404,
        );

        $this->contractInputs[$subjectId][$groupId] = $contractId;

        $this->contractSearch[$subjectId][$groupId] =
            $this->contractOptions[$contractId];
    }

    public function addSelectedContracts(int $subjectId, int $groupId): void
    {
        abort_unless($this->hasSubject($subjectId), 404);
        abort_unless($this->hasGroup($groupId), 404);

        $work = $this->matrix[$subjectId][$groupId]['work'] ?? [];

        foreach (array_keys($this->contractOptions) as $contractId) {
            $contractId = (int) $contractId;

            if ($contractId < 1 || isset($work[$contractId])) {
                continue;
            }

            $work[$contractId] = [
                'contract_id' => $contractId,
                'quantity' => 1,
            ];
        }

        $this->matrix[$subjectId][$groupId]['work'] = $work;

        $this->notifyParent();
    }

    public function addContract(int $subjectId, int $groupId): void
    {
        abort_unless($this->hasSubject($subjectId), 404);
        abort_unless($this->hasGroup($groupId), 404);

        $contractId = (int) ($this->contractInputs[$subjectId][$groupId] ?? 0);
        $quantity = (int) ($this->quantityInputs[$subjectId][$groupId] ?? 1);

        abort_unless(
            array_key_exists($contractId, $this->contractOptions),
            422,
        );

        if ($contractId < 1 || $quantity < 1) {
            return;
        }

        $this->matrix[$subjectId][$groupId]['work'] ??= [];

        if (isset($this->matrix[$subjectId][$groupId]['work'][$contractId])) {
            return;
        }

        $this->matrix[$subjectId][$groupId]['work'][$contractId] = [
            'contract_id' => $contractId,
            'quantity' => $quantity,
        ];

        $this->contractInputs[$subjectId][$groupId] = '';
        $this->quantityInputs[$subjectId][$groupId] = 1;

        $this->notifyParent();
    }

    public function removeContract(
        int $subjectId,
        int $groupId,
        int $contractId,
    ): void {
        abort_unless($this->hasSubject($subjectId), 404);
        abort_unless($this->hasGroup($groupId), 404);

        unset($this->matrix[$subjectId][$groupId]['work'][$contractId]);

        $this->notifyParent();
    }

    private function hasSubject(int $id): bool
    {
        return collect($this->subjects)->contains(
            fn(array $subject) => (int) $subject['id'] === $id
        );
    }

    private function hasGroup(int $id): bool
    {
        return collect($this->groups)->contains(
            fn(array $group) => (int) $group['id'] === $id
        );
    }

    private function notifyParent(): void
    {
        logger()->debug('AssignmentMatrix dispatching event');

        $this->dispatch(
            'assignment-matrix-updated',
            state: $this->matrix,
            notes: $this->notes,
        );
    }

    public function render()
    {
        return view(
            // 'dpb-mod-tasks::livewire.task-batch.assignment-matrix'
            'dpb-mod-tasks::livewire.task-batch.assignment-matrix-t'
        );
    }
}
