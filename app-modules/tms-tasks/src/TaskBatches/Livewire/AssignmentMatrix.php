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

    public array $personInputs = [];
    public array $peopleOptions = [];

    public array $personSearch = [];

    public array $quantityInputs = [];

    public function mount(
        array $subjects = [],
        array $groups = [],
        array $state = [],
    ): void {
        $this->subjects = $subjects;
        $this->groups = $groups;
        $this->matrix = $state;

        // Ensure every subject/group combination has a cell.
        foreach ($this->subjects as $subject) {
            $subjectId = (int) $subject['id'];

            foreach ($this->groups as $group) {
                $groupId = (int) $group['id'];

                $this->matrix[$subjectId][$groupId] = [
                    'people' => array_values(
                        $state[$subjectId][$groupId]['people'] ?? []
                    ),
                ];

                $this->personInputs[$subjectId][$groupId] = '';
                $this->quantityInputs[$subjectId][$groupId] = 1;
            }
        }
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

        $existingPersonIds = array_map(
            fn(array $person) => (int) $person['person_id'],
            $people,
        );

        foreach (array_keys($this->peopleOptions) as $personId) {
            $personId = (int) $personId;

            if (in_array($personId, $existingPersonIds, true)) {
                continue;
            }

            $people[] = [
                'person_id' => $personId,
                'quantity' => 1,
            ];

            $existingPersonIds[] = $personId;
        }

        $this->matrix[$subjectId][$groupId]['people'] = $people;

        $this->notifyParent();
    }

    public function addPerson(int $subjectId, int $groupId): void
    {
        // Only allow cells belonging to the supplied matrix.
        abort_unless($this->hasSubject($subjectId), 404);
        abort_unless($this->hasGroup($groupId), 404);

        $personId = (int) (
            $this->personInputs[$subjectId][$groupId] ?? 0
        );

        $quantity = (int) (
            $this->quantityInputs[$subjectId][$groupId] ?? 1
        );

        if ($personId < 1 || $quantity < 1) {
            return;
        }

        // TODO: Verify that this person is eligible for this task,
        // and that the current user is authorized to edit the batch.

        $people = $this->matrix[$subjectId][$groupId]['people'] ?? [];

        // Prevent accidental duplicates in the same cell.
        foreach ($people as $person) {
            if ((int) $person['person_id'] === $personId) {
                return;
            }
        }

        $this->matrix[$subjectId][$groupId]['people'][] = [
            'person_id' => $personId,
            'quantity' => $quantity,
        ];

        $this->personInputs[$subjectId][$groupId] = '';
        $this->personSearch[$subjectId][$groupId] = '';
        $this->quantityInputs[$subjectId][$groupId] = 1;

        $this->notifyParent();
    }

    public function removePerson(
        int $subjectId,
        int $groupId,
        int $index,
    ): void {
        abort_unless($this->hasSubject($subjectId), 404);
        abort_unless($this->hasGroup($groupId), 404);

        unset($this->matrix[$subjectId][$groupId]['people'][$index]);

        $this->matrix[$subjectId][$groupId]['people'] = array_values(
            $this->matrix[$subjectId][$groupId]['people'] ?? []
        );

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
        );
    }

    public function render()
    {
        return view(
            'dpb-mod-tasks::livewire.task-batch.assignment-matrix'
        );
    }
}
