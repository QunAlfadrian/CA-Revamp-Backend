<?php

namespace App\Traits;

use App\Models\Campaign;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait BelongsToManyCampaigns {
    public function campaigns() {
        return $this->campaignsRelation;
    }

    public function campaignsRelation(): BelongsToMany {
        return $this->belongsToMany(Campaign::class, 'requested_books', 'campaign_id', 'book_id');
    }
}
