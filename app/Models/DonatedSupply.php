<?php

namespace App\Models;

use App\Traits\BelongsToDonatedItem;
use App\Traits\BelongsToRequestedSupply;
use App\Traits\ModelHelpers;
use Illuminate\Database\Eloquent\Relations\Pivot;

class DonatedSupply extends Pivot {
    // model helper
    use ModelHelpers;

    // relationship helper
    use BelongsToDonatedItem;
    use BelongsToRequestedSupply;

    protected $primaryKey = 'donated_supply_id';
    protected $table = 'donated_supplies';
    public $incrementing = false;

    public function donatedItemId(): string {
        return $this->donated_item_id;
    }

    public function requestedSupplyId(): string {
        return $this->requested_supply_id;
    }

    public function quantity(): string {
        return $this->quantity;
    }
}
