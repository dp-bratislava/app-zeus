<ul class="list space-y-2">
    {{-- @foreach ($vehicleModels as $id => $title)
        <li class="bg-white shadow rounded-lg p-4 hover:shadow-lg transition"
            wire:click="$dispatch('filter-requested', {modelId: {{ $id }}})">
            {{ $id . ' ' . $title }}
        </li>
    @endforeach --}}


    @foreach ($vehicleModels as $id => $vehicleModel)
        <li class="bg-white shadow rounded-lg p-4 hover:shadow-lg transition"
            wire:click="$dispatch('filter-requested', {modelId: {{ $id }}})">
            <div class="flex justify-between items-center">
                <div class="text-left pe-2">
                    {{ $vehicleModel->id . ' ' . $vehicleModel->title}}
                </div>
                <div class="text-left">
                    {{ "[" . $vehicleModel->vehicles->count() . "]" }}
                </div>
            </div>
        </li>
    @endforeach
</ul>
