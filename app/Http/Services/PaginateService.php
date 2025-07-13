<?php

namespace App\Http\Services;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class PaginateService {
    public function paginateCollection(
        Collection $collection,
        int $page = 1,
        int $perPage = 10
    ) {
        $offset = ($page - 1) * $perPage;

        return new LengthAwarePaginator(
            items: $collection->slice($offset, $perPage)->values(),
            total: $collection->count(),
            perPage: $perPage,
            currentPage: $page,
            options: [
                'path' => request()->url(),
                'query' => request()->query()
            ]
        );
    }
}
