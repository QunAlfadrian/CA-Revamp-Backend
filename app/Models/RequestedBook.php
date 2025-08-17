<?php

namespace App\Models;

use App\Traits\BelongsToBook;
use App\Traits\HasCampaign;
use App\Traits\ModelHelpers;
use Illuminate\Database\Eloquent\Relations\Pivot;

class RequestedBook extends Pivot {
    // model helper
    use ModelHelpers;

    // relationship helper
    use HasCampaign;
    use BelongsToBook;

    protected $table = 'requested_books';

    public function campaignId(): string {
        return $this->campaign_id;
    }

    public function bookId(): string {
        return $this->book_id;
    }

    public function requestedQuantity(): string {
        return (string) $this->requested_quantity;
    }

    public function donatedQuantity(): string {
        return (string) $this->donated_quantity;
    }
}
