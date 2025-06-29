<?php

namespace App\Traits;

use App\Models\RequestedSupply;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasManyRequestedSupplies {
    public function requestedSupplies() {
        return $this->requestedSuppliesRelation;
    }

    public function requestedSuppliesRelation(): HasMany {
        return $this->hasMany(RequestedSupply::class);
    }

    public function isRequesting(RequestedSupply $supply): bool {
        return $this->requestedSupplies()->contains($supply);
    }

    public function requestSupply(RequestedSupply $supply) {
        return $this->requestedSuppliesRelation()->save($supply);
    }
}
