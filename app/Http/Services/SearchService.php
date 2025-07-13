<?php
namespace App\Http\Services;

use Illuminate\Support\Collection;

class SearchService {
    /**
     * Create a new class instance.
     */
    public function __construct() {
        //
    }

    public function fuzzySearch(
        Collection $collection,
        string $searchTerm, array $fields,
        int $threshold = 3
    ): Collection {
        $term = strtolower($searchTerm);

        return $collection->filter(function ($item) use ($term, $fields, $threshold) {
            foreach ($fields as $field) {
                $value = strtolower(data_get($item, $field, ''));

                if (levenshtein($term, $value) <= $threshold) {
                    return true;
                }
            }

            return false;
        });
    }
}
