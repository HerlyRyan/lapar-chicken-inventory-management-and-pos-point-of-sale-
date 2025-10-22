import './bootstrap';
import Alpine from 'alpinejs';
window.Alpine = Alpine;

// === UNIVERSAL TABLE COMPONENT ===
Alpine.data('sortableTable', (initialData) => ({
    rows: initialData || [],
    sortColumn: '',
    sortDirection: 'asc',
    isLoading: false,
    endpoint: window.location.pathname, // gunakan URL halaman aktif

    async fetchData() {
        this.isLoading = true;
        window.dispatchEvent(new CustomEvent('loading:start'))
        try {
            const params = new URLSearchParams({
                search: Alpine.store('table').search || '',
                sort_by: this.sortColumn || '',
                sort_dir: this.sortDirection || 'asc',
                ...Alpine.store('table').filters,
            });
            const response = await fetch(`${this.endpoint}?${params.toString()}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            const result = await response.json();
            this.rows = result.data || [];
            const pagination = document.querySelector('.pagination-wrapper');
            if (pagination) pagination.innerHTML = result.links || '';
        } catch (error) {
            console.error('Fetch error:', error);
        } finally {
            this.isLoading = false;
            window.dispatchEvent(new CustomEvent('loading:end'));
        }
    },

    get filteredRows() {
        // tetap dukung filter frontend
        const search = Alpine.store('table').search.toLowerCase();
        const filters = Alpine.store('table').filters;

        return this.rows.filter(row => {
            const matchesSearch = !search || Object.values(row).some(value => {
                if (typeof value === 'string') return value.toLowerCase().includes(search);
                if (typeof value === 'object' && value?.name)
                    return value.name.toLowerCase().includes(search);
                return false;
            });

            const matchesFilters = Object.entries(filters).every(([key, val]) => {
                if (val === '' || val === null || val === undefined) return true
                const column = row[key]

                // 🧩 Konversi nilai ke bentuk string yang seragam
                let normalizedColumn = column
                if (typeof column === 'boolean') normalizedColumn = column ? '1' : '0'
                else if (typeof column === 'number') normalizedColumn = String(column)
                else if (typeof column === 'string') normalizedColumn = column.toLowerCase()
                else if (typeof column === 'object' && column !== null && column.name)
                    normalizedColumn = column.name.toLowerCase()
                else normalizedColumn = String(column ?? '').toLowerCase()

                const normalizedFilter = String(val).toLowerCase()

                return normalizedColumn === normalizedFilter
            });

            return matchesSearch && matchesFilters;
        });
    },

    get sortedRows() {
        const rows = this.filteredRows;
        if (!this.sortColumn) return rows;

        return [...rows].sort((a, b) => {
            const valA = this.getValue(a, this.sortColumn);
            const valB = this.getValue(b, this.sortColumn);
            return this.sortDirection === 'asc'
                ? valA.localeCompare(valB)
                : valB.localeCompare(valA);
        });
    },

    getValue(row, column) {
        let value = row[column];
        if (Array.isArray(value)) return value.map(v => v.name || '').join(', ').toLowerCase();
        if (typeof value === 'object' && value) return value.name?.toLowerCase() || '';
        if (typeof value === 'string') return value.toLowerCase();
        if (typeof value === 'number') return value.toString();
        return '';
    },

    sortBy(column) {
        this.sortDirection = (this.sortColumn === column && this.sortDirection === 'asc') ? 'desc' : 'asc';
        this.sortColumn = column;
        this.fetchData(); // 🔁 trigger backend sort
    },

    init() {
        this.$watch('$store.table.search', () => this.debouncedFetch());
        this.$watch('$store.table.filters', () => this.fetchData(), { deep: true });
    },

    debouncedFetch: Alpine.debounce(function () {
        this.fetchData();
    }, 400)
}));

Alpine.store('table', {
    search: '',
    filters: {},
    reset() {
        this.search = '';
        for (const key in this.filters) this.filters[key] = '';
    }
});

// === GLOBAL LOADING EVENT HELPER ===
function showGlobalLoading(show = true) {
    window.dispatchEvent(new CustomEvent(show ? 'loading:start' : 'loading:end'));
}

// Tambahkan ke global supaya bisa dipanggil dari mana pun
window.showGlobalLoading = showGlobalLoading;

Alpine.start();