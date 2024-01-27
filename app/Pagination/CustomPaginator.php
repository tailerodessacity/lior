<?php

namespace App\Pagination;

use App\Models\Post;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class CustomPaginator extends Paginator
{
    /**
     * @param string $page
     * @param int $perPage
     * @return CustomPaginator
     */
    public static function create(Builder $query, string $table, string $page, int $perPage)
    {
        $offset = ($page - 1) * $perPage;

        $columns = $query->getQuery()->getColumns();

        $columnsString = implode(', ', $columns);

//        $sql = "select {$columnsString}
//from (SELECT {$columnsString}, ROW_NUMBER() OVER (ORDER BY created_at) AS num FROM posts) as OrderedRows
//where `num` between $offset + 1 and $offset + $perPage
//limit 10";

        try {
            $query->seletRaw("SELECT {$columnsString}, ROW_NUMBER() OVER (ORDER BY created_at) AS num FROM posts ) as OrderedRows");
            $posts = $query->whereBetween('num', [$offset + 1, $offset + $perPage])
                ->take($perPage)
                ->toSql();

            return new static($posts, $perPage);

        }catch (\Exception $e){

        }


//
//        $posts = Post::from(DB::raw('(SELECT id, title, preview, detail, ROW_NUMBER() OVER (ORDER BY created_at) AS num FROM posts) as OrderedRows'))
//            ->whereBetween('num', [$offset + 1, $offset + $perPage])
//            ->take($perPage)
//            ->paginate();


    }
}
