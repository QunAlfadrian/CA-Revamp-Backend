<?php

namespace App\Traits;

use App\Models\Role;
use App\Models\Campaign;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait ReviewManyCampaigns {
    public function reviewedCampaigns() {
        if (!($this->isActingAs(Role::admin() || $this->isActingAs(Role::superAdmin())))) {
            throw new \Exception('Only admins can access this resources.');
        }

        return $this->reviewedCampaignRelation;
    }

    public function reviewedCampaignRelation(): HasMany {
        if (!($this->isActingAs(Role::admin() || $this->isActingAs(Role::superAdmin())))) {
            throw new \Exception('Only admins can access this resources.');
        }

        return $this->hasMany(Campaign::class, 'reviewe_id');
    }

    public function isReviewerOf(Campaign $campaign): bool {
        if (!($this->isActingAs(Role::admin() || $this->isActingAs(Role::superAdmin())))) {
            throw new \Exception('Only admins can access this resources.');
        }

        return $this->reviewedCampaigns()->contains($campaign);
    }

    public function reviewerOf(Campaign $campaign) {
        if (!($this->isActingAs(Role::admin() || $this->isActingAs(Role::superAdmin())))) {
            throw new \Exception('Only admins can access this resources.');
        }

        return $this->reviewedCampaignRelation()->save($campaign);
    }
}
