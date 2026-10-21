<div class="relative isolate max-h-[70vh] overflow-auto rounded-lg border border-gray-200 dark:border-gray-700">
    <table class="w-full table-fixed border-collapse text-sm">
        <thead class="sticky top-0 z-10 bg-gray-50 dark:bg-gray-800">
            <tr>
                {{-- Sticky group label column --}}
                <th scope="col"
                    class="sticky left-0 z-20 w-64 border-b border-r border-gray-200 bg-gray-50 px-3 py-3 text-left font-medium text-gray-700 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200">
                    Skupina podzákazky
                </th>

                {{-- One column per vehicle --}}
                @foreach ($subjects as $subject)
                    <th scope="col" wire:key="vehicle-header-{{ $subject['id'] }}"
                        class="w-56 border-b border-r border-gray-200 px-3 py-3 text-left align-top font-medium text-gray-700 dark:border-gray-700 dark:text-gray-200">
                        <div class="max-w-full whitespace-normal break-words">
                            {{ $subject['label'] }}
                        </div>

                        <div class="mt-2">
                            <textarea rows="2" wire:model.live.debounce.300ms="notes.{{ (int) $subject['id'] }}" placeholder="Poznámka..."
                                aria-label="Note for {{ $subject['label'] }}"
                                class="w-full max-w-full resize-y rounded-md border-gray-300 px-2 py-1 text-xs shadow-sm dark:border-gray-600 dark:bg-gray-900"></textarea>
                        </div>
                    </th>
                @endforeach
            </tr>
        </thead>

        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse ($groups as $group)
                @php
                    $groupId = (int) $group['id'];
                @endphp

                <tr wire:key="group-row-{{ $groupId }}" class="group hover:bg-gray-50 dark:hover:bg-gray-800/70">
                    {{-- Sticky group name --}}
                    <th scope="row"
                        class="sticky left-0 z-10 w-80 border-r border-b border-gray-200 bg-white px-3 py-3 text-left align-top font-medium text-gray-800 group-hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 dark:group-hover:bg-gray-800">
                        <div class="whitespace-normal break-words">
                            {{ $group['label'] }}
                        </div>
                    </th>

                    {{-- One cell for each vehicle --}}
                    @foreach ($subjects as $subject)
                        @php
                            $subjectId = (int) $subject['id'];

                            $assignedWork = $matrix[$subjectId][$groupId]['work'] ?? [];
                        @endphp

                        <td wire:key="matrix-cell-{{ $subjectId }}-{{ $groupId }}"
                            class="w-56 border-r border-b border-gray-200 p-2 align-top dark:border-gray-700">
                            <div class="flex min-h-16 min-w-0 flex-col gap-2">
                                {{-- Assigned work --}}
                                @foreach ($assignedWork as $contractId => $contract)
                                    @php
                                        $contractId = (int) $contractId;
                                        $contractLabel = $contractOptions[$contractId] ?? (string) $contractId;
                                        $labelParts = explode(' - ', $contractLabel, 2);
                                        $contractName = count($labelParts) > 1 ? $labelParts[1] : $labelParts[0];
                                        $contractPid = count($labelParts) > 1 ? $labelParts[0] : null;
                                    @endphp

                                    <div wire:key="assignment-{{ $subjectId }}-{{ $groupId }}-{{ $contractId }}"
                                        class="flex min-w-0 items-center gap-2 rounded-md border border-gray-200 bg-white p-2 dark:border-gray-700 dark:bg-gray-900">
                                        <div class="min-w-0 flex-1">
                                            <div
                                                class="break-words text-sm font-medium text-gray-800 dark:text-gray-100">
                                                {{ $contractName }}
                                            </div>

                                            {{-- @if ($contractPid !== null)
                                                <div class="break-words text-xs text-gray-500 dark:text-gray-400">
                                                    {{ $contractPid }}
                                                </div>
                                            @endif --}}
                                        </div>

                                        @if ($group['is_scalable'] ?? false)
                                            <input type="number" min="1"
                                                wire:model.live="matrix.{{ $subjectId }}.{{ $groupId }}.work.{{ $contractId }}.quantity"
                                                aria-label="Quantity for {{ $contractLabel }} on {{ $subject['label'] }} — {{ $group['label'] }}"
                                                class="w-16 shrink-0 rounded-md border-gray-300 px-2 py-1 text-sm dark:border-gray-600 dark:bg-gray-800" />
                                        @endif

                                        <button type="button"
                                            wire:click="removeContract({{ $subjectId }}, {{ $groupId }}, {{ $contractId }})"
                                            title="Zmazať {{ $contractLabel }}"
                                            aria-label="Zmazať {{ $contractLabel }} z {{ $subject['label'] }} — {{ $group['label'] }}"
                                            class="shrink-0 rounded-md p-1 text-gray-400 hover:bg-danger-50 hover:text-danger-600 dark:hover:bg-gray-800">
                                            <x-heroicon-o-x-mark class="h-4 w-4" />
                                        </button>
                                    </div>
                                @endforeach

                                {{-- Add selected contracts --}}
                                <div class="flex justify-center pt-1">
                                    <button type="button"
                                        wire:click="addSelectedContracts({{ $subjectId }}, {{ $groupId }})"
                                        @disabled(empty($contractOptions)) title="Add selected contracts"
                                        aria-label="Add selected contracts to {{ $subject['label'] }} — {{ $group['label'] }}"
                                        class="inline-flex items-center justify-center rounded-md p-1.5 text-primary-600 hover:bg-primary-50 disabled:cursor-not-allowed disabled:opacity-40 dark:hover:bg-gray-800">
                                        <x-heroicon-o-user-plus class="h-5 w-5" />
                                    </button>
                                </div>
                            </div>
                        </td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($subjects) + 1 }}"
                        class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                        Nie sú dostupné žiadne skupiny podzákazok.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
