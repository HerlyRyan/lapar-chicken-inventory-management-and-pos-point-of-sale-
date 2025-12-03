<aside :class="{ 'hidden': !sidebarOpen }" x-cloak x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0 -translate-x-full" x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 -translate-x-full"
    class="w-72 bg-white border-r border-gray-200 shadow-lg md:block transition-all duration-200">

    <x-side-bar/>
</aside>

{{-- Mobile sidebar (overlay) --}}
<div x-show="sidebarOpen" x-cloak class="md:hidden fixed inset-0 z-50">
    <div @click="sidebarOpen = false" class="absolute inset-0 bg-black/50 backdrop-blur-sm"></div>
    <aside class="relative w-64 bg-white h-full shadow-2xl">
        <x-side-bar mobile="true"/>
    </aside>
</div>
