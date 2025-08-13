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
            ->withPivot(['requested_quantity', 'donated_quantity'])->withTimeStamps();
    }

    public function isRequestingBook(Book $book) {
        return $this->books()->contains($book);
    }

    public function donateBook(Book $book, int $amount = 1) {
        if (!$this->isRequestingBook($book)) {
            throw new \Exception('Cannot donate to non requested book');
        }

        $pivot = $this->booksRelation()->find($book->isbn())->pivot;
        $donatedQty = $pivot->donated_quantity;

        $this->booksRelation()->updateExistingPivot($book->isbn(), [
            'donated_quantity' => $donatedQty + $amount
        ]);

        return $this->save();
    }

    public function requestBook(Book $book, int $amount = 1) {
        // update requested item qty
        $requestedItemQty = $this->requestedItemQuantity() + $amount;
        $this->update([
            'requested_item_quantity' => $requestedItemQty
        ]);

        // if not already requesting the book, create a new pivot
        if (!$this->isRequestingBook($book)) {
            $this->booksRelation()->attach($book->isbn(), [
                'requested_quantity' => $amount
            ]);
            return $this->save();
        }

        // if already requesting the book, update the pivot
        $quantity = $this->books()->where('isbn', $book->isbn())->first()->pivot->requested_quantity;
        $this->booksRelation()->updateExistingPivot($book->isbn(), [
            'requested_quantity' => $quantity + $amount
        ]);
        return $this->save();
    }
}
