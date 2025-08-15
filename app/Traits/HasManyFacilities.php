<?php

namespace App\Traits;

use App\Models\Facility;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasManyFacilities {
    public function facilities() {
        return $this->facilitiesRelation;
    }

    public function facilitiesRelation(): HasMany {
        return $this->hasMany(Facility::class, 'campaign_id', 'campaign_id');
    }

    public function attachFacility(Facility $facility) {
        return $this->facilitiesRelation()->save($facility);
    }
}
