<?php

namespace App\Pagination;

use App\Models\Post;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\DB;

class CustomPaginator extends Paginator
{
    /**
     * @param string $page
     * @param int $perPage
     * @return CustomPaginator
     */
    public static function create(string $page, int $perPage)
    {
        $offset = ($page - 1) * $perPage;

        $posts = Post::from(DB::raw('(SELECT title, preview, detail, ROW_NUMBER() OVER (ORDER BY created_at) AS num FROM posts) as OrderedRows'))
            ->whereBetween('num', [$offset + 1, $offset + $perPage])
            ->take($perPage)
            ->get();

        return new static($posts, $perPage);
    }
}
