<?php

namespace App\Traits;

use App\Models\RequestedSupply;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToRequestedSupply {
    public function requestedSupply(): RequestedSupply {
        return $this->donatedItemRelation;
    }

    public function requestedSupplyRelation(): BelongsTo {
        return $this->belongsTo(RequestedSupply::class, 'requested_supply_id', 'requested_supply_id');
    }

    public function associatedToSupply(RequestedSupply $model) {
        return $this->donatedItem()->matches($model);
    }

    public function attachRequestedSupply(RequestedSupply $model) {
        return $this->donatedItemRelation()->associate($model)->save();
    }
}
