<?php

namespace App\Traits;

use App\Models\Campaign;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToCampaign {
    public function campaign(): Campaign {
        return $this->campaignRelation;
    }

    public function campaignRelation(): BelongsTo {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'campaign_id');
    }

    public function partOfCampaign(Campaign $campaign): bool {
        return $this->campaign()->matches($campaign);
    }

    public function associateToCampaign(Campaign $campaign) {
        return $this->campaignRelation()->associate($campaign);
    }
}
