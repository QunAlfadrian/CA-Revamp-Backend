<?php

namespace App\Traits;

use App\Models\Campaign;
use App\Models\Fund;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasManyFunds {
    public function funds() {
        return $this->fundsRelation;
    }

    public function fundsRelation(): HasMany {
        return $this->hasMany(Fund::class);
    }

    public function addFund(Fund $fund) {
        return $this->fundsRelation()->save($fund);
    }
}
