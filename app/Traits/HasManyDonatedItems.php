<?php

namespace App\Traits;

use App\Models\DonatedItem;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasManyDonatedItems {
    public function donatedItems() {
        return $this->donatedItemsRelation;
    }

    public function donatedItemsRelation(): HasMany {
        return $this->hasMany(DonatedItem::class, 'donation_id', 'donation_id');
    }

    public function addDonatedItem(DonatedItem $item) {
        return $this->donatedItemsRelation()->save($item);
    }
}
