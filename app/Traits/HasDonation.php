<?php

namespace App\Traits;

use App\Models\Donation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasDonation {
    public function donation(): Donation {
        return $this->donationRelation;
    }

    public function donationRelation(): BelongsTo {
        return $this->belongsTo(Donation::class, 'donation_id', 'donation_id');
    }

    public function partOfDonation(Donation $donation): bool {
        return $this->donation()->matches($donation);
    }

    public function assignToDonation(Donation $donation) {
        return $this->donationRelation()->associate($donation);
    }
}
