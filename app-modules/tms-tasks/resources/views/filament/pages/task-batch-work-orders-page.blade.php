<x-filament-panels::page class="h-full">
    <div class="grid grid-cols-6 gap-4 h-max overflow-hidden">
        <div class="overflow-hidden flex flex-col">
            <div class="pb-2">
                <livewire:dpb.wtff.worktime-assignment-picker-widget.employee-picker wire:model="selectedEmployees" :showEmployeePids="true" :sortByLastName="true" :demandedDates="$demandedDates" />
            </div>
        </div>
        <div class="col-span-4 overflow-hidden flex flex-col gap-y-2">
            <div>
                <livewire:dpb-ui-components.date-picker :is-multi-mode="$this::DATEPICKER_MULTI_MODE" />
            </div>
            <div class="overflow-y-auto">
                @foreach ($this->taskItemGroups ?? [] as $taskItemGroup)
                    <x-filament::button
                        wire:click="selectTaskItemGroup({{ $taskItemGroup->id }})"
                        color="{{  $this->selectedTaskItemGroupId === $taskItemGroup->id ? 'success' : 'gray' }}"
                        class="justify-start mb-1"
                    >
                        #{{ $taskItemGroup->id }} [{{ $taskItemGroup->code }}] {{ $taskItemGroup->title }} ({{ $this->getCountOfAssignedOperations($taskItemGroup->id) }})
                    </x-filament::button>
                @endforeach
            </div>
            <div class="pb-2">
                {{ $this->form }}
            </div>
            <div class="grid grid-cols-2 gap-4 max-h-[70vh]">
                <div class="h-full overflow-y-auto">
                    @foreach ($this->categories ?? [] as $category)
                        @include('dpb-wtf-tms-bridge::filament.pages.work-order-page.category', ['category' => $category, 'isOpen' => $this->isCategoryOpened($category)])
                    @endforeach
                </div>
                @if ($this->selectedCategoryId)
                    <div class="max-h-[70vh] overflow-y-auto">
                        @foreach ($this->operations ?? [] as $operation)
                            @include('dpb-wtf-tms-bridge::filament.pages.work-order-page.operation', ['operation' => $operation])
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
        <div class="min-h-full flex flex-col p-4">
            <livewire:wtf-ui.assignment-container-component />
            <div>
                {{ $this->taskBatchAssignAction()->button() }}
                {{ $this->submitWorkOrderAction()->button() }}
            </div>
        </div>
    </div>
</x-filament-panels::page>
