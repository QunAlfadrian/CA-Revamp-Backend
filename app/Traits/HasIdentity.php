<?php

namespace App\Traits;

use App\Models\Identity;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasIdentity {
    public function identity(): ?Identity {
        return $this->identityRelation;
    }

    public function identityRelation(): HasOne {
        return $this->hasOne(Identity::class, 'user_id', 'user_id');
    }

    public function isOwnerOf(Identity $identity): bool {
        return $this->identity()->matches($identity);
    }

    public function ownerOf(Identity $identity): bool {
        return $this->identityRelation()->save($identity);
    }
}
