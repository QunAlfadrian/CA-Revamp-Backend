<?php

namespace App\Traits;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait BelongsToManyUsers {
    public function users() {
        return $this->userRelation;
    }

    public function userRelation(): BelongsToMany {
        return $this->belongsToMany(User::class, 'role_user', 'role_id', 'user_id');
    }

    // public function isActingAs(User $user): bool {
    //     return $this->roles()->matches($role);
    // }

    // public function actingAs(Role $role) {
    //     return $this->roleRelation()->associate($role);
    // }
}
