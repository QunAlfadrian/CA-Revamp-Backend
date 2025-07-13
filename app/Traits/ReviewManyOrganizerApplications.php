<?php

namespace App\Traits;

use App\Models\OrganizerApplication;
use App\Models\Role;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait ReviewManyOrganizerApplications {
    public function reviewedApplications() {
        if (!($this->isActingAs(Role::admin()) || $this->isActingAs(Role::superAdmin()))) {
            throw new \Exception('You do not have permissions to access this resources.');
        }

        return $this->reviewedApplicationsRelation;
    }

    public function reviewedApplicationsRelation(): HasMany {
        if (!($this->isActingAs(Role::admin()) || $this->isActingAs(Role::superAdmin()))) {
            throw new \Exception('You do not have permissions to access this resources.');
        }

        return $this->hasMany(OrganizerApplication::class, 'reviewed_by');
    }

    // public function isReviewerOf(OrganizerApplication $application): bool {
    //     if (!($this->isActingAs(Role::admin() || $this->isActingAs(Role::superAdmin())))) {
    //         throw new \Exception('You do not have permissions to access this resources.');
    //     }

    //     return $this->reviewedApplications()->contains($application);
    // }

    public function reviewerOfApplication(OrganizerApplication $application) {
        if (!($this->isActingAs(Role::admin()) || $this->isActingAs(Role::superAdmin()))) {
            throw new \Exception('You do not have permissions to access this resources.');
        }

        return $this->reviewedApplicationsRelation()->save($application);
    }
}
