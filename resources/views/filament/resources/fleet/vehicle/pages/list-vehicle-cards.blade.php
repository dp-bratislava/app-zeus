<x-filament::page>
    <div class="grid grid-cols-6 gap-4">
        <div class="col-span-1 bg-gray-200 p-4">
            @livewire('fleet-vehicle-model-list', ['vehicleModels' => $vehicleModels])
        </div>

        <div class="col-span-5 bg-gray-100 p-4">
            <div class="grid grid-cols-6 gap-4">
                @foreach ($vehicles as $vehicle)
                    {{-- vehicle cards --}}
                    @livewire('fleet-vehicle-card', ['vehicle' => $vehicle])
                @endforeach
            </div>
        </div>
    </div>
</x-filament::page>
