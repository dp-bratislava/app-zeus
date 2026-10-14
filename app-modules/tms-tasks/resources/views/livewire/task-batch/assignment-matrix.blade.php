<div class="space-y-4">
    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
        <table class="w-full min-w-max divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th
                        class="sticky left-0 z-10 min-w-48 bg-gray-50 px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:bg-gray-800 dark:text-gray-100">
                        Vehicle
                    </th>

                    @foreach ($groups as $group)
                        <th wire:key="group-heading-{{ $group['id'] }}"
                            class="min-w-64 px-4 py-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">
                            {{ $group['label'] }}
                        </th>
                    @endforeach
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                @forelse ($subjects as $subject)
                    <tr wire:key="subject-row-{{ $subject['id'] }}">
                        <th class="sticky left-0 z-10 bg-white px-4 py-4 text-left align-top dark:bg-gray-900">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $subject['label'] }}
                            </div>
                        </th>

                        @foreach ($groups as $group)
                            @php
                                $subjectId = (int) $subject['id'];
                                $groupId = (int) $group['id'];

                                $assignedPeople = $matrix[$subjectId][$groupId]['people'] ?? [];
                            @endphp

                            <td wire:key="matrix-cell-{{ $subjectId }}-{{ $groupId }}"
                                class="min-w-64 px-4 py-3 align-top">
                                <div class="space-y-3">
                                    {{-- People already assigned to this cell --}}
                                    @forelse ($assignedPeople as $index => $person)
                                        @php
                                            $personId = (int) $person['person_id'];
                                            $personLabel =
                                                $peopleOptions[$personId] ??
                                                ($peopleOptions[(string) $personId] ?? "Unknown person (#{$personId})");
                                        @endphp

                                        <div wire:key="assignment-{{ $subjectId }}-{{ $groupId }}-{{ $personId }}"
                                            class="rounded-lg border border-gray-200 p-3 dark:border-gray-700">
                                            <div class="flex items-start justify-between gap-2">
                                                <div class="min-w-0 flex-1">
                                                    <div
                                                        class="break-words text-sm font-medium text-gray-900 dark:text-gray-100">
                                                        {{ $personLabel }}
                                                    </div>
                                                </div>

                                                <button type="button"
                                                    wire:click="removePerson({{ $subjectId }}, {{ $groupId }}, {{ $index }})"
                                                    title="Remove person" aria-label="Remove {{ $personLabel }}"
                                                    class="shrink-0 rounded-md p-1 text-gray-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950">
                                                    <x-heroicon-o-x-mark class="h-4 w-4" />
                                                </button>
                                            </div>

                                            <div class="mt-3 flex items-center justify-between gap-3">
                                                <label
                                                    for="quantity-{{ $subjectId }}-{{ $groupId }}-{{ $personId }}"
                                                    class="text-xs text-gray-500 dark:text-gray-400">
                                                    Quantity
                                                </label>

                                                <input
                                                    id="quantity-{{ $subjectId }}-{{ $groupId }}-{{ $personId }}"
                                                    type="number" min="1" step="1"
                                                    wire:model.live="matrix.{{ $subjectId }}.{{ $groupId }}.people.{{ $index }}.quantity"
                                                    class="w-20 rounded-lg border-gray-300 bg-white text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" />
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-sm text-gray-400 dark:text-gray-500">
                                            No people assigned
                                        </p>
                                    @endforelse

                                    {{-- Add all people selected in the global ContractPicker --}}
                                    <div
                                        class="flex items-center justify-between gap-2 border-t border-gray-100 pt-3 dark:border-gray-800">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            Add selected people
                                        </span>

                                        <button type="button"
                                            wire:click="addSelectedPeople({{ $subjectId }}, {{ $groupId }})"
                                            @disabled(empty($peopleOptions)) title="Add all selected people"
                                            aria-label="Add selected people to {{ $subject['label'] }}, {{ $group['label'] }}"
                                            class="inline-flex items-center justify-center rounded-lg border border-gray-300 p-2 text-primary-600 hover:bg-primary-50 disabled:cursor-not-allowed disabled:opacity-40 dark:border-gray-600 dark:hover:bg-gray-800">
                                            <x-heroicon-o-user-plus class="h-5 w-5" />
                                        </button>
                                    </div>
                                </div>
                            </td>
                        @endforeach
                    </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($groups) + 1 }}"
                                class="px-4 py-10 text-center text-sm text-gray-500 dark:text-gray-400">
                                Select at least one vehicle to configure assignments.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if (empty($peopleOptions))
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Select one or more contracts above to enable adding people to the matrix.
            </p>
        @endif
    </div>
