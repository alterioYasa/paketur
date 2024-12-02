<?php

namespace App\Http\Traits;

use Exception;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

trait DatabaseTrait
{
    public function getTableColumns(string $table, array $execpt)
    {
        if (Schema::hasTable($table)) {
            $columns = Schema::connection('mysql')->getColumnListing($table);
            $columns = array_diff($columns, $execpt);
            return $columns;
        } else {
            return false;
        }
    }

    public function getSortingParams($request, $validSortFields = ['name'], $defaultSortField = 'id', $defaultSortDirection = 'asc')
    {
        $sortField = in_array($request->sort_field, $validSortFields) ? $request->sort_field : $defaultSortField;
        $sortDirection = in_array($request->sort_direction, ['asc', 'desc']) ? $request->sort_direction : $defaultSortDirection;

        return [
            'sort_field' => $sortField,
            'sort_direction' => $sortDirection,
        ];
    }
}
