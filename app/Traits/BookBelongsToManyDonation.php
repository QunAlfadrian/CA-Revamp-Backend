<?php

namespace App\Traits;

use App\Models\Donation;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait BookBelongsToManyDonation {
    public function donations() {
        return $this->donationsRelation;
    }

    public function donationsRelation(): BelongsToMany {
        return $this->belongsToMany(Donation::class, 'donated_books', 'book_id', 'donated_item_id');
    }
}
