<?php

namespace App\Traits;

use App\Models\DonatedItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToDonatedItem {
    public function donatedItem(): DonatedItem {
        return $this->donatedItemRelation;
    }

    public function donatedItemRelation(): BelongsTo {
        return $this->belongsTo(DonatedItem::class, 'donated_item_id', 'donated_item_id');
    }

    public function associatedTo(DonatedItem $model) {
        return $this->donatedItem()->matches($model);
    }

    public function attachDonatedItem(DonatedItem $model) {
        return $this->donatedItemRelation()->associate($model)->save();
    }
}
