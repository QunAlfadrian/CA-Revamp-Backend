<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasReviewer {
    public function reviewer(): ?User {
        return $this->reviewerRelation;
    }

    public function reviewerRelation(): BelongsTo {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function isReviewedBy(User $user) {
        return $this->reviewer()->matches($user);
    }

    public function reviewedBy(User $user) {
        return $this->reviewerRelation()->associate($user)->save();
    }

    public function reviewerName(): string {
        return $this->reviewer()->name();
    }

    public function reviewedAt(): string {
        return $this->reviewed_at ? $this->reviewed_at->format('d-m-Y H:i:s') : '';
    }
}
