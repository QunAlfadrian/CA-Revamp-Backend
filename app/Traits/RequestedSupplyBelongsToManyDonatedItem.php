<?php

namespace App\Traits;

use App\Models\DonatedItem;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait RequestedSupplyBelongsToManyDonatedItem {
    public function donatedItems() {
        return $this->donatedItemsRelation;
    }

    public function donatedItemsRelation(): BelongsToMany {
        return $this->belongsToMany(
            DonatedItem::class,
            'donated_supplies',
            'requested_supply_id',
            'donated_item_id'
        )->withPivot([
            'quantity'
        ])->withTimestamps();
    }
}
