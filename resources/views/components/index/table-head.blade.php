@props(['columns' => []])

<thead class="bg-gray-50">
    <tr>
        <th
            class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer select-none hover:bg-gray-100">
            No
        </th>
        @foreach ($columns as $col)
            <th scope="col"
                class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer select-none hover:bg-gray-100"
                @click="sortBy('{{ $col['key'] }}')">

                <div class="flex items-center space-x-1">
                    <span>{{ $col['label'] }}</span>

                    <template x-if="sortColumn === '{{ $col['key'] }}'">
                        <span>
                            <svg x-show="sortDirection === 'asc'" xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 15l7-7 7 7" />
                            </svg>
                            <svg x-show="sortDirection === 'desc'" xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </template>
                </div>
            </th>
        @endforeach

        <th
            class="px-6 py-3 text-left text-xs font-medium text-gray-700 uppercase tracking-wider cursor-pointer select-none hover:bg-gray-100">
            Aksi
        </th>
    </tr>
</thead>
