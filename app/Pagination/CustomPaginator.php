<?php

namespace App\Pagination;

use App\Models\Post;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CustomPaginator extends AbstractPaginator
{
    /**
     * @param string $page
     * @param int $perPage
     * @return Collection
     */
    public static function create(string $page, int $perPage): Collection
    {
        $offset = ($page - 1) * $perPage;

        $posts = Post::from(DB::raw('(SELECT id, title, preview, detail, ROW_NUMBER() OVER (ORDER BY created_at) AS num FROM posts) as OrderedRows'))
            ->whereBetween('num', [$offset + 1, $offset + $perPage])
            ->take($perPage)
            ->get();

        return $posts;
    }
}
