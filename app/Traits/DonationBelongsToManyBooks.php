<?php

namespace App\Traits;

use App\Models\Book;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait DonationBelongsToManyBooks {
    public function books() {
        return $this->booksRelation;
    }

    public function booksRelation(): BelongsToMany {
        return $this->belongsToMany(Book::class, 'donated_books', 'donated_item_id', 'book_id')
            ->withPivot(['quantity'])->withTimeStamps();
    }

    public function alreadyHas(Book $book) {
        return $this->books()->contains($book);
    }

    public function attachBook(Book $book, int $amount = 1) {
        // update donated item qty
        $totalQuantity = $this->quantity() + $amount;
        $this->update([
            'quantity' => $totalQuantity
        ]);

        // if not already has book, create new pivot
        if (!$this->alreadyHas($book)) {
            $this->booksRelation()->attach($book->isbn(), [
                'quantity' => $amount
            ]);
            return $this->save();
        }

        // if already attached to book, update pivot
        $bookQuantity = $this->books()
            ->where('isbn', $book->isbn())
            ->first()
            ->pivot
            ->quantity;
        $this->booksRelation()->updateExistingPivot($book->isbn(), [
            'quantity' => $bookQuantity + $amount
        ]);
        return $this->save();
    }
}

