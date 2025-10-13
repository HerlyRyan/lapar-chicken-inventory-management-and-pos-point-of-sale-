<?php

/**
 * Helper functions for table sorting and filtering functionality
 */

if (!function_exists('sortColumn')) {
    /**
     * Generate a sortable column header with appropriate classes and icons
     * 
     * @param string $column Current column name
     * @param string $label Text label for the column header
     * @param string $sortColumn Active sort column
     * @param string $sortDirection Current sort direction
     * @return string HTML for the sortable column
     */
    function sortColumn($column, $label, $sortColumn, $sortDirection)
    {
        $url = request()->fullUrlWithQuery([
            'sort' => $column,
            'direction' => $column === $sortColumn && $sortDirection === 'asc' ? 'desc' : 'asc'
        ]);
        
        $icon = '';
        if ($column === $sortColumn) {
            $icon = $sortDirection === 'asc' ? 
                '<i class="bi bi-caret-up-fill ms-1"></i>' : 
                '<i class="bi bi-caret-down-fill ms-1"></i>';
        }
        
        $class = $column === $sortColumn ? 'text-primary sortable-column sort-' . $sortDirection : 'sortable-column';
        
        return '<a href="' . $url . '" class="' . $class . '">' . $label . $icon . '</a>';
    }
}
