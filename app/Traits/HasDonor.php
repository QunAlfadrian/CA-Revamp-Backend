<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasDonor {
    public function donor(): User {
        return $this->donorRelation;
    }

    public function donorRelation(): BelongsTo {
        return $this->belongsTo(User::class, 'donor_id');
    }

    public function isDonatedBy(User $user): bool {
        return $this->donor()->matches($user);
    }

    public function donatedBy(User $user) {
        return $this->donorRelation()->associate($user);
    }
}
