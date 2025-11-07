<x-filament-panels::page>
    {{-- Sidebar --}}
    <aside
        x-data="{ open: false }"
        class="hidden md:block w-64 shrink-0 bg-white border-r border-slate-200"
        aria-label="Sidebar"
    >
        <div class="h-full flex flex-col">
            <div class="px-4 py-6 border-b">
                <a href="#" class="text-lg font-semibold">My App</a>
            </div>

            <nav class="py-4 px-2 flex-1 overflow-auto">
                <ul class="space-y-1">
                    <li><a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-slate-50">Dashboard</a></li>
                    <li><a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-slate-50">Tickets</a></li>
                    <li><a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-slate-50">Tasks</a></li>
                    <li><a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-slate-50">Inspections</a></li>
                    <li><a href="#" class="flex items-center gap-3 px-3 py-2 rounded hover:bg-slate-50">Settings</a></li>
                </ul>
            </nav>

            <div class="px-4 py-4 border-t">
                <a href="#" class="text-sm text-slate-600">Account</a>
            </div>
        </div>
    </aside>

    {{-- Mobile topbar + offcanvas sidebar toggle --}}
    <div class="md:hidden fixed top-3 left-3 z-40">
        <button
            id="mobileToggle"
            aria-label="Open menu"
            class="inline-flex items-center justify-center p-2 rounded bg-white shadow"
            onclick="document.getElementById('mobileSidebar').classList.toggle('hidden')"
        >
            <!-- hamburger -->
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
        </button>
    </div>

    <aside id="mobileSidebar" class="md:hidden fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 p-4 hidden">
        <div class="flex items-center justify-between mb-4">
            <a href="#" class="text-lg font-semibold">My App</a>
            <button onclick="document.getElementById('mobileSidebar').classList.add('hidden')" aria-label="Close">
                ✕
            </button>
        </div>

        <nav class="space-y-2">
            <a href="#" class="block px-3 py-2 rounded hover:bg-slate-50">Dashboard</a>
            <a href="#" class="block px-3 py-2 rounded hover:bg-slate-50">Tickets</a>
            <a href="#" class="block px-3 py-2 rounded hover:bg-slate-50">Tasks</a>
            <a href="#" class="block px-3 py-2 rounded hover:bg-slate-50">Inspections</a>
            <a href="#" class="block px-3 py-2 rounded hover:bg-slate-50">Settings</a>
        </nav>
    </aside>

    {{-- Main content area (scrollable) --}}
    <main class="flex-1 h-screen flex flex-col overflow-hidden">
        {{-- optional sticky header --}}
        <header class="flex-shrink-0 border-b border-slate-200 bg-white">
            <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
                <h1 class="text-lg font-semibold">{{ $title ?? 'Page title' }}</h1>
                <div class="flex items-center gap-3">
                    {{-- place actions, search, user avatar etc --}}
                </div>
            </div>
        </header>

        {{-- scrollable content region --}}
        <section class="flex-1 overflow-auto bg-slate-50">
            <div class="max-w-7xl mx-auto p-6">
                {{-- content slot --}}
                {{ $slot }}
            </div>
        </section>
    </main>
</x-filament-panels::page>
