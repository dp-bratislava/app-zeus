<x-filament::page>
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
        @foreach ($this->getLinks() as $link)
            <a href="{{ $link['url'] }}"
               class="group block p-6 rounded-2xl bg-white shadow-sm hover:shadow-lg transition">
                <div class="flex items-center gap-4">
                    <x-filament::icon
                        :icon="$link['icon']"
                        class="w-10 h-10 text-primary-600"
                    />
                    <div>
                        <div class="text-lg font-semibold">{{ $link['label'] }}</div>
                    </div>
                </div>
            </a>
        @endforeach
    </div>
</x-filament::page>