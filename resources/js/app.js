import './bootstrap';
import Alpine from 'alpinejs';
window.Alpine = Alpine;

// Komponen global untuk tabel sortable
Alpine.data('sortableTable', (data) => ({
    rows: data || [],
    sortColumn: '',
    sortDirection: 'asc',

    get filteredRows() {
        const search = Alpine.store('table').search.toLowerCase()
        const filters = Alpine.store('table').filters

        return this.rows.filter(row => {
            // 🔍 Search logic (cek semua kolom string)
            const matchesSearch = !search || Object.values(row).some(value => {
                if (typeof value === 'string') return value.toLowerCase().includes(search)
                if (typeof value === 'object' && value !== null && value.name)
                    return value.name.toLowerCase().includes(search)
                return false
            })

            // 🎯 Filter logic (cek berdasarkan filters aktif)
            const matchesFilters = Object.entries(filters).every(([key, val]) => {
                if (!val) return true
                const column = row[key]
                if (typeof column === 'string') return column.toLowerCase() === val.toLowerCase()
                if (typeof column === 'object' && column !== null)
                    return column.name && column.name.toLowerCase() === val.toLowerCase()
                return false
            })

            return matchesSearch && matchesFilters
        })
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

        // Jika value adalah array (misal: roles), ambil gabungan nama
        if (Array.isArray(value)) {
            return value.map(v => v.name || '').join(', ').toLowerCase();
        }

        // Jika value adalah object (misal: branch)
        if (typeof value === 'object' && value !== null) {
            return value.name ? value.name.toLowerCase() : '';
        }

        // String atau number
        if (typeof value === 'string') return value.toLowerCase();
        if (typeof value === 'number') return value.toString();

        return '';
    },

    sortBy(column) {
        if (this.sortColumn === column) {
            this.sortDirection = this.sortDirection === 'asc' ? 'desc' : 'asc'
        } else {
            this.sortColumn = column
            this.sortDirection = 'asc'
        }
    }
}))

Alpine.store('table', {
    search: '',
    filters: {},
    reset() {
        this.search = ''
        this.filters = {}
    }
})

Alpine.start();