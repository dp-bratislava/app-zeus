<x-filament-panels::page>

    {{-- {{ dd($dailyExpeditions) }} --}}

    <x-filament::button icon="heroicon-m-chevron-double-left" color="primary" tag="a"
        href="{{ route('filament.admin.resources.daily-expeditions.bulk-create') }}">
        Create
    </x-filament::button>

    {{-- Date filter --}}
    <input type="date" wire:model="filterDate" class="border p-2" />
{{-- {{ dd($dailyExpeditions) }} --}}
    {{-- Display filtered data --}}
    @foreach ($dailyExpeditions as $vehicleModel => $dailyExpedition)
        <div class="grid grid-cols-4 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white shadow rounded-lg p-4">
                <h2 class="text-lg font-semibold">{{ $vehicleModel }}</h2>
                <p class="text-gray-600 mt-2">
                    @foreach ($dailyExpedition as $dailyExpeditionData)
                        {{-- {{ dd($dailyExpeditionData) }} --}}
                        <div @class([
                            'bg-green-200' => $dailyExpeditionData['state'] === 'in-service',
                            'bg-yellow-200' => $dailyExpeditionData['state'] === 'split-servie',
                            'bg-red-200' => $dailyExpeditionData['state'] === 'out-of-service',
                        ])>
                            <div class="inline">{{ $dailyExpeditionData['vehicle']['codes'][0]['code'] }}</div>
                            <div class="inline">{{ $dailyExpeditionData['service'] }}</div>
                        </div>
                    @endforeach
                </p>
            </div>
        </div>
    @endforeach
</x-filament-panels::page>
