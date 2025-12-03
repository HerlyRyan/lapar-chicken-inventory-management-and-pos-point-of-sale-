@props(['title' => 'Tambah/Edit Data', 'type' => 'add'])

<div class="bg-gradient-to-r from-orange-600 via-orange-700 to-red-700 px-6 py-6">
    <div class="flex items-center">
        <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center mr-3">
            @if ($type == 'add')
                <i class="bi bi-plus-square text-white"></i>
            @else
                <i class="bi bi-pencil-square text-white"></i>
            @endif
        </div>
        <h2 class="text-xl font-bold text-white">Form {{ $title }}</h2>
    </div>
</div>
