<?php

namespace App\Traits;

use App\Models\Campaign;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait HasCampaign {
    public function campaign(): Campaign {
        return $this->campaignRelation;
    }

    public function campaignRelation(): BelongsTo {
        return $this->belongsTo(Campaign::class, 'campaign_id', 'campaign_id');
    }

    public function isPartOfCampaign(Campaign $campaign): bool {
        return $this->campaign()->matches($campaign);
    }

    public function partOfCampaign(Campaign $campaign) {
        return $this->campaignRelation()->associate($campaign);
    }
}
