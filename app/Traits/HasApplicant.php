<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasApplicant {
    public function applicant(): ?User {
        return $this->applicantRelation;
    }

    public function applicantRelation(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isAppliedBy(User $user): bool {
        return $this->applicant()->matches($user);
    }

    public function appliedBy(User $user) {
        return $this->applicantRelation()->associate($user);
    }
}
