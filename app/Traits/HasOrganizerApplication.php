<?php

namespace App\Traits;

use App\Models\OrganizerApplication;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait HasOrganizerApplication {
    public function organizerApplication(): ?OrganizerApplication {
        return $this->organizerApplicationRelation;
    }

    public function organizerApplicationRelation(): HasOne {
        return $this->hasOne(OrganizerApplication::class);
    }

    public function isOwnerOfApplication(OrganizerApplication $application): bool {
        return $this->organizerApplication()->matches($application);
    }

    public function assignApplication(OrganizerApplication $application) {
        return $this->organizerApplicationRelation()->save($application);
    }
}
