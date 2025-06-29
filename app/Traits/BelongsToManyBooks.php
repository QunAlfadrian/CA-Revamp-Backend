<?php

namespace App\Traits;

use App\Models\Book;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait BelongsToManyBooks {
    public function books() {
        return $this->booksRelation;
    }

    public function booksRelation(): BelongsToMany {
        return $this->belongsToMany(Book::class, 'requested_books', 'campaign_id', 'book_id')
            ->withPivot(['quantity', 'donated_quantity'])->withTimeStamps();
    }

    public function isRequesting(Book $book) {
        return $this->books()->contains($book);
    }

    public function donateBook(Book $book, int $amount = 1) {
        if (!$this->isRequesting($book)) {
            throw new \Exception('Cannot donate to non requested book');
        }

        $donatedQty = $this->books()->where('isbn', $book->isbn())->first()->pivot->donated_quantity;
        $this->booksRelation()->updateExistingPivot($book->isbn(), [
            'donated_quantity' => $donatedQty + $amount
        ]);
    }

    public function requestBook(Book $book, int $amount = 1) {
        // if not already requesting the book, create a new pivot
        if (!$this->isRequesting($book)) {
            return $this->booksRelation()->attach($book->isbn, [
                'quantity' => $amount
            ]);
        }

        // if laready requesting the book, update the pivot
        $quantity = $this->books()->where('isbn', $book->isbn())->first()->pivot->quantity;
        $this->booksRelation()->updateExistingPivot($book->isbn(), [
            'quantity' => $quantity + $amount
        ]);
    }
}
