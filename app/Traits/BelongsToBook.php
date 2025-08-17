<?php

namespace App\Traits;

use App\Models\Book;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToBook {
    public function book(): Book {
        return $this->bookRelation;
    }

    public function bookRelation(): BelongsTo {
        return $this->belongsTo(Book::class, 'book_id', 'book_id');
    }

    public function isPartOfBook(Book $book): bool {
        return $this->book()->matches($book);
    }

    public function associateToBook(Book $book) {
        return $this->bookRelation()->associate($book);
    }
}
