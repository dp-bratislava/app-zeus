<x-filament::page>
    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">

        {{-- TASK-MS PANEL --}}
        <a href="{{ filament()->getPanel('task-ms')->getUrl() }}"
           class="group block p-6 rounded-2xl bg-white shadow-sm hover:shadow-lg transition">
            <div class="flex items-center gap-4">
                <x-heroicon-o-clipboard-document-list class="w-10 h-10 text-primary-600" />
                <div>
                    <div class="text-lg font-semibold">Task-MS</div>
                    <div class="text-sm text-gray-500 group-hover:text-gray-700">Task management panel</div>
                </div>
            </div>
        </a>

        {{-- WTF PANEL --}}
        <a href="{{ filament()->getPanel('wtf')->getUrl() }}"
           class="group block p-6 rounded-2xl bg-white shadow-sm hover:shadow-lg transition">
            <div class="flex items-center gap-4">
                <x-heroicon-o-wrench-screwdriver class="w-10 h-10 text-primary-600" />
                <div>
                    <div class="text-lg font-semibold">WTF</div>
                    <div class="text-sm text-gray-500 group-hover:text-gray-700">Workshop / tools panel</div>
                </div>
            </div>
        </a>

        {{-- ADMIN PANEL --}}
        <a href="{{ filament()->getPanel('admin')->getUrl() }}"
           class="group block p-6 rounded-2xl bg-white shadow-sm hover:shadow-lg transition">
            <div class="flex items-center gap-4">
                <x-heroicon-o-shield-check class="w-10 h-10 text-primary-600" />
                <div>
                    <div class="text-lg font-semibold">Admin</div>
                    <div class="text-sm text-gray-500 group-hover:text-gray-700">Administration</div>
                </div>
            </div>
        </a>

        {{-- DATAHUB PANEL --}}
        <a href="{{ filament()->getPanel('datahub')->getUrl() }}"
           class="group block p-6 rounded-2xl bg-white shadow-sm hover:shadow-lg transition">
            <div class="flex items-center gap-4">
                <x-heroicon-o-database class="w-10 h-10 text-primary-600" />
                <div>
                    <div class="text-lg font-semibold">Datahub</div>
                    <div class="text-sm text-gray-500 group-hover:text-gray-700">Data management</div>
                </div>
            </div>
        </a>

    </div>
</x-filament::page>