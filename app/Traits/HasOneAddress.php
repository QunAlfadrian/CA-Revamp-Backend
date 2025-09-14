<?php

namespace App\Traits;

use App\Models\Address;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasOneAddress {
    public function address(): Address {
        return $this->addressRelation;
    }

    public function addressRelation(): HasOne {
        return $this->hasOne(Address::class, 'campaign_id', 'campaign_id');
    }

    public function isAssociatedToAddress(Address $address): bool {
        return $this->address()->matches($address);
    }

    public function associateAddress(Address $address) {
        return $this->addressRelation()->associate($address);
    }
}
