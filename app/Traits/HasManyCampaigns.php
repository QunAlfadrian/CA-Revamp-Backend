<?php

namespace App\Traits;

use App\Models\Campaign;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasManyCampaigns {
    public function campaigns() {
        if (! $this->isActingAs(Role::organizer())) {
            throw new \Exception('Only organizer can access this resources.');
        }

        return $this->campaignsRelation;
    }

    public function campaignsRelation(): HasMany {
        if (! $this->isActingAs(Role::organizer())) {
            throw new \Exception('Only organizer can access this resources.');
        }

        return $this->hasMany(Campaign::class, 'organizer_id', 'user_id');
    }

    public function isOrganizerOf(Campaign $campaign): bool {
        if (! $this->isActingAs(Role::organizer())) {
            throw new \Exception('Only organizer can access this resources.');
        }

        return $this->campaigns()->contains($campaign);
    }

    public function organizerOf(Campaign $campaign) {
        if (! $this->isActingAs(Role::organizer())) {
            throw new \Exception('Only organizer can access this resources.');
        }

        return $this->campaignsRelation()->save($campaign);
    }
}
