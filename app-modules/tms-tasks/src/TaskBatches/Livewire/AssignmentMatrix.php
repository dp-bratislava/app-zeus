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
     *             'people' => [
     *                 ['person_id' => 42, 'quantity' => 1],
     *             ],
     *         ],
     *     ],
     * ]
     */
    public array $matrix = [];
    public array $notes = [];

    public array $personInputs = [];
    public array $peopleOptions = [];

    public array $personSearch = [];

    public array $quantityInputs = [];

    public function mount(
        array $subjects = [],
        array $groups = [],
        array $state = [],
        array $peopleOptions = [],
    ): void {
        $this->subjects = $subjects;
        $this->groups = $groups;
        $this->peopleOptions = $peopleOptions;
        $this->matrix = $state;

        // Normalize people collections to use person_id as the key.
        foreach ($this->matrix as $subjectId => $subjectGroups) {
            foreach ($subjectGroups as $groupId => $cell) {
                $normalizedPeople = [];

                foreach ($cell['people'] ?? [] as $person) {
                    if (! is_array($person) || ! isset($person['person_id'])) {
                        continue;
                    }

                    $personId = (int) $person['person_id'];

                    $normalizedPeople[$personId] = [
                        'person_id' => $personId,
                        'quantity' => (int) ($person['quantity'] ?? 1),
                    ];
                }

                $this->matrix[$subjectId][$groupId]['people'] = $normalizedPeople;
            }
        }

        foreach ($this->subjects as $subject) {
            $subjectId = (int) $subject['id'];

            $this->notes[$subjectId] ??= '';

            foreach ($this->groups as $group) {
                $groupId = (int) $group['id'];

                $this->matrix[$subjectId][$groupId] ??= [
                    'people' => [],
                ];

                $this->matrix[$subjectId][$groupId]['people'] ??= [];
                $this->personInputs[$subjectId][$groupId] ??= '';
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

    public function selectPerson(
        int $subjectId,
        int $groupId,
        int $personId,
    ): void {
        abort_unless($this->hasSubject($subjectId), 404);
        abort_unless($this->hasGroup($groupId), 404);
        abort_unless(
            array_key_exists($personId, $this->peopleOptions),
            404,
        );

        $this->personInputs[$subjectId][$groupId] = $personId;

        $this->personSearch[$subjectId][$groupId] =
            $this->peopleOptions[$personId];
    }

    public function addSelectedPeople(int $subjectId, int $groupId): void
    {
        abort_unless($this->hasSubject($subjectId), 404);
        abort_unless($this->hasGroup($groupId), 404);

        $people = $this->matrix[$subjectId][$groupId]['people'] ?? [];

        foreach (array_keys($this->peopleOptions) as $personId) {
            $personId = (int) $personId;

            if ($personId < 1 || isset($people[$personId])) {
                continue;
            }

            $people[$personId] = [
                'person_id' => $personId,
                'quantity' => 1,
            ];
        }

        $this->matrix[$subjectId][$groupId]['people'] = $people;

        $this->notifyParent();
    }

    public function addPerson(int $subjectId, int $groupId): void
    {
        abort_unless($this->hasSubject($subjectId), 404);
        abort_unless($this->hasGroup($groupId), 404);

        $personId = (int) ($this->personInputs[$subjectId][$groupId] ?? 0);
        $quantity = (int) ($this->quantityInputs[$subjectId][$groupId] ?? 1);

        abort_unless(
            array_key_exists($personId, $this->peopleOptions),
            422,
        );

        if ($personId < 1 || $quantity < 1) {
            return;
        }

        $this->matrix[$subjectId][$groupId]['people'] ??= [];

        if (isset($this->matrix[$subjectId][$groupId]['people'][$personId])) {
            return;
        }

        $this->matrix[$subjectId][$groupId]['people'][$personId] = [
            'person_id' => $personId,
            'quantity' => $quantity,
        ];

        $this->personInputs[$subjectId][$groupId] = '';
        $this->quantityInputs[$subjectId][$groupId] = 1;

        $this->notifyParent();
    }

    public function removePerson(
        int $subjectId,
        int $groupId,
        int $personId,
    ): void {
        abort_unless($this->hasSubject($subjectId), 404);
        abort_unless($this->hasGroup($groupId), 404);

        unset($this->matrix[$subjectId][$groupId]['people'][$personId]);

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
