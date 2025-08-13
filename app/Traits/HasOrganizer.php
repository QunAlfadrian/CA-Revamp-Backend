<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasOrganizer {
    public function organizer(): User {
        return $this->organizerRelation;
    }

    public function organizerRelation(): BelongsTo {
        return $this->belongsTo(User::class, 'organizer_id', 'user_id');
    }

    public function isOrganizedBy(User $user) {
        return $this->organizer()->matches($user);
    }

    public function organizedBy(User $user) {
        return $this->organizerRelation()->associate($user);
    }

    public function organizerName(): string {
        return $this->organizer()->name();
    }
}
