<?php

namespace App\Traits;

use App\Models\Donation;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasManyDonations {
    public function donations() {
        return $this->donationsRelation;
    }

    public function donationsRelation(): HasMany {
        return $this->hasMany(Donation::class);
    }

    public function hasDonation(Donation $donation): bool {
        return $this->donations()->contains($donation);
    }

    public function addDonation(Donation $donation) {
        return $this->donationsRelation()->save($donation);
    }
}
