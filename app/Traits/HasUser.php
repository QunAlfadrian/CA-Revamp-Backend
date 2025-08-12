<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasUser {
    public function user(): User {
        return $this->userRelation;
    }

    public function userRelation(): BelongsTo {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function isOwnedBy(User $user): bool {
        return $this->user()->matches($user);
    }

    public function ownedBy(User $user) {
        if (!is_null($this->user())) {
            return;
        }

        if (!$this->isOwnedBy($user)) {
            return;
        }

        return $this->userRelation()->associate($user);
    }
}
