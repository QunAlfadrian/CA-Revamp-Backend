<?php

namespace App\Traits;

use App\Models\Campaign;
use App\Models\Fund;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait CampaignHasManyFunds {
    public function funds() {
        return $this->fundsRelation;
    }

    public function fundsRelation(): HasMany {
        return $this->hasMany(Fund::class, 'campaign_id', 'campaign_id');
    }

    public function addFund(Fund $fund) {
        return $this->fundsRelation()->save($fund);
    }
}
