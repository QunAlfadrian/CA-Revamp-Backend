<?php

namespace App\Traits;

use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait BelongsToManyRoles {
    public function roles() {
        return $this->roleRelation;
    }

    public function roleRelation(): BelongsToMany {
        return $this->belongsToMany(Role::class);
    }

    public function isActingAs(Role $role): bool {
        return $this->roles()->contains($role);
    }

    public function actingAs(Role $role) {
        $this->roleRelation()->attach($role);
        return $this->save();
    }
}
