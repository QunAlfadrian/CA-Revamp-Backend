<?php

namespace App\Traits;

use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait BelongsToManyRoles {
    public function roles() {
        return $this->roleRelation;
    }

    /**
     * The roles that belong to the BelongsToManyRoles
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roleRelation(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user', 'user_id', 'role_id');
    }

    public function isActingAs(Role $role): bool {
        return $this->roles()->contains($role);
    }

    public function actingAs(Role $role) {
        $this->roleRelation()->attach($role->id());
        return $this->save();
    }
}
