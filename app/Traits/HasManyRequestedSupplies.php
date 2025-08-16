<?php

namespace App\Traits;

use App\Models\RequestedSupply;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasManyRequestedSupplies {
    public function requestedSupplies() {
        return $this->requestedSuppliesRelation;
    }

    public function requestedSuppliesRelation(): HasMany {
        return $this->hasMany(RequestedSupply::class, 'campaign_id', 'campaign_id');
    }

    public function isRequestingSupply(RequestedSupply $supply): bool {
        return $this->requestedSupplies()->contains($supply);
    }

    public function requestSupply(RequestedSupply $supply) {
        return $this->requestedSuppliesRelation()->save($supply);
    }
}
