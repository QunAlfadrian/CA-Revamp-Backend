<?php

namespace App\Traits;

use App\Models\Facility;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasManyFacilities {
    public function facilities() {
        return $this->facilitiesRelation;
    }

    public function facilitiesRelation(): HasMany {
        return $this->hasMany(Facility::class);
    }

    public function attachFacility(Facility $facility) {
        return $this->facilitiesRelation()->save($facility);
    }
}
