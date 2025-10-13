<?php

namespace App\Traits;

trait TableFilterTrait
{
    /**
     * Apply filtering, sorting and pagination to a query builder
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param array $searchableColumns Array of columns to search ['name', 'description', etc]
     * @param array $sortableColumns Array of columns that can be sorted ['name', 'created_at', etc]
     * @param string|null $defaultSortColumn Default column to sort by (null for no default)
     * @param string $defaultSortDirection Default sort direction ('asc' or 'desc')
     * @return \Illuminate\Pagination\LengthAwarePaginator
     */
    protected function applyFilterSortPaginate($query, array $searchableColumns = [], array $sortableColumns = [], $defaultSortColumn = null, $defaultSortDirection = 'asc')
    {
        // Apply search filtering
        if (request()->has('search') && !empty(request('search'))) {
            $searchTerm = request('search');
            $query->where(function($q) use ($searchableColumns, $searchTerm) {
                foreach ($searchableColumns as $column) {
                    // Handle relationship columns (e.g. 'category.name')
                    if (strpos($column, '.') !== false) {
                        [$relation, $relationColumn] = explode('.', $column);
                        $q->orWhereHas($relation, function($subQuery) use ($relationColumn, $searchTerm) {
                            $subQuery->where($relationColumn, 'like', "%{$searchTerm}%");
                        });
                    } else {
                        $q->orWhere($column, 'like', "%{$searchTerm}%");
                    }
                }
            });
        }

        // Apply status filter if provided
        if (request()->has('status') && request('status') !== 'all') {
            $statusValue = request('status') === 'active' ? 1 : 0;
            $query->where('is_active', $statusValue);
        }

        // Apply date range filter if provided
        if (request()->has('date_from') && !empty(request('date_from'))) {
            $query->whereDate('created_at', '>=', request('date_from'));
        }

        if (request()->has('date_to') && !empty(request('date_to'))) {
            $query->whereDate('created_at', '<=', request('date_to'));
        }

        // Apply custom filters
        foreach (request()->all() as $key => $value) {
            if (strpos($key, 'filter_') === 0 && !empty($value)) {
                $filterColumn = substr($key, 7); // remove 'filter_' prefix
                
                // Handle relationship filtering
                if (strpos($filterColumn, '.') !== false) {
                    [$relation, $relationColumn] = explode('.', $filterColumn);
                    $query->whereHas($relation, function($subQuery) use ($relationColumn, $value) {
                        $subQuery->where($relationColumn, $value);
                    });
                } else {
                    $query->where($filterColumn, $value);
                }
            }
        }

        // Apply sorting
        $sortColumn = request('sort', $defaultSortColumn);
        $sortDirection = request('direction', $defaultSortDirection);
        
        if ($sortColumn && in_array($sortColumn, $sortableColumns)) {
            $query->orderBy($sortColumn, $sortDirection);
        } elseif ($defaultSortColumn) {
            $query->orderBy($defaultSortColumn, $defaultSortDirection);
        }

        // Return paginated results
        return $query->paginate(15)->appends(request()->except('page'));
    }
}
