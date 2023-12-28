<?php

namespace App\Pagination;

use Illuminate\Pagination\Paginator;

class CustomPaginator extends Paginator
{
    /**
     * @param $model
     * @param $perPage
     * @param $currentPage
     * @param array $options
     * @return static
     */
    public static function create($model, $perPage, $currentPage = null, array $options = [])
    {
        $model = app($model);
        $prevPage = $currentPage - 1;
        $query = $model::where('id', '>', $prevPage * $perPage);

        $results = $query->orderBy('id')
            ->take($perPage)
            ->get();

        return new static($results, $perPage, $currentPage, $options);
    }
}
