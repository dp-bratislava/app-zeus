<div class="space-y-4">
    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
        <table class="w-full table-auto divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th
                        class="sticky left-0 z-10 whitespace-nowrap border-r border-b border-gray-200 bg-gray-50 px-2 py-2 text-left text-sm font-semibold dark:border-gray-700 dark:bg-gray-800">
                        Vehicle
                    </th>

                    @foreach ($groups as $group)
                        <th wire:key="group-heading-{{ $group['id'] }}"
                            class="whitespace-nowrap border-r border-b border-gray-200 px-2 py-2 text-center text-sm font-semibold dark:border-gray-700">
                            {{ $group['label'] }}
                        </th>
                    @endforeach

                    <th
                        class="whitespace-nowrap border-r border-b border-gray-200 px-2 py-2 text-center text-sm font-semibold dark:border-gray-700">
                        Pozn
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-200 bg-white dark:divide-gray-700 dark:bg-gray-900">
                @forelse ($subjects as $subject)
                    <tr wire:key="subject-row-{{ $subject['id'] }}"
                        class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-800/70">
                        <th class="sticky left-0 z-10 bg-white px-4 py-4 text-left align-top dark:bg-gray-900">
                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                {{ $subject['label'] }}
                            </div>
                        </th>

                        @foreach ($groups as $group)
                            @php
                                $subjectId = (int) $subject['id'];
                                $groupId = (int) $group['id'];

                                $assignedWork = $matrix[$subjectId][$groupId]['work'] ?? [];
                            @endphp

                            <td wire:key="matrix-cell-{{ $subjectId }}-{{ $groupId }}"
                                class="border-r border-b border-gray-200 px-2 py-2 align-top dark:border-gray-700">
                                <div class="space-y-2">

                                    @foreach ($assignedWork as $contractId => $person)
                                        @php
                                            $contractId = (int) $contractId;

                                            $personLabel =
                                                $contractOptions[$contractId] ??
                                                ($contractOptions[(string) $contractId] ?? "Unknown person (#{$contractId})");

                                            $labelParts = explode(' - ', $personLabel, 2);
                                            $personPid = $labelParts[0] ?? '';
                                            $personLastName = $labelParts[1] ?? $personLabel;
                                        @endphp

                                        <div wire:key="assignment-{{ $subjectId }}-{{ $groupId }}-{{ $contractId }}"
                                            class="flex items-center gap-2 rounded-lg border border-gray-200 px-2 py-2 dark:border-gray-700">
                                            <div class="min-w-0 flex-1">
                                                <div
                                                    class="truncate text-sm font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $personLastName }}
                                                </div>

                                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $personPid }}
                                                </div>
                                            </div>

                                            @if ($group['is_scalable'] ?? false)
                                                <input
                                                    wire:key="quantity-{{ $subjectId }}-{{ $groupId }}-{{ $contractId }}"
                                                    type="number" min="1" step="1"
                                                    wire:model.live="matrix.{{ $subjectId }}.{{ $groupId }}.work.{{ $contractId }}.quantity"
                                                    aria-label="Quantity for {{ $personLastName }}"
                                                    class="w-14 rounded-md border-gray-300 px-1.5 py-1 text-sm dark:border-gray-600 dark:bg-gray-800 dark:text-gray-100" />
                                            @endif

                                            <button type="button"
                                                wire:click="removePerson({{ $subjectId }}, {{ $groupId }}, {{ $contractId }})"
                                                title="Remove person" aria-label="Remove {{ $personLastName }}"
                                                class="shrink-0 rounded p-1 text-gray-400 hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950">
                                                <x-heroicon-o-x-mark class="h-4 w-4" />
                                            </button>
                                        </div>
                                    @endforeach

                                    <div class="flex justify-center">
                                        <button type="button"
                                            wire:click="addSelectedWork({{ $subjectId }}, {{ $groupId }})"
                                            @disabled(empty($contractOptions)) title="Add selected work"
                                            aria-label="Add selected work to {{ $subject['label'] }}, {{ $group['label'] }}"
                                            class="inline-flex items-center justify-center rounded-md p-1.5 text-primary-600 hover:bg-primary-50 disabled:cursor-not-allowed disabled:opacity-40 dark:hover:bg-gray-800">
                                            <x-heroicon-o-user-plus class="h-5 w-5" />
                                        </button>
                                    </div>
                                </div>
                            </td>
                        @endforeach

                        <td class="border-r border-b border-gray-200 px-2 py-2 align-top dark:border-gray-700">
                            <input type="text" wire:model.live.debounce.300ms="notes.{{ (int) $subject['id'] }}"
                                placeholder="Add note..." aria-label="Note for {{ $subject['label'] }}"
                                class="w-40 rounded-md border-gray-300 bg-transparent px-2 py-1 text-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:text-gray-100" />
                        </td>
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

    @if (empty($contractOptions))
        <p class="text-sm text-gray-500 dark:text-gray-400">
            Select one or more contracts above to enable adding work to the matrix.
        </p>
    @endif
</div>
