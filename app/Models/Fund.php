<?php

namespace App\Models;

use App\Traits\HasCampaign;
use App\Traits\ModelHelpers;
use Illuminate\Database\Eloquent\Model;

class Fund extends Model {
    use ModelHelpers;

    /** Relation Helpers */
    use HasCampaign;

    public $keyType = 'string';
    public $incrementing = 'false';

    public function id(): string {
        return $this->id;
    }

    public function orderId(): string {
        return $this->id;
    }

    public function amount(): string {
        return (string)$this->amount;
    }

    public function status(): string {
        return $this->status;
    }

    public function snapToken(): string {
        return $this->snap_token;
    }

    public function redirectUrl(): string {
        return $this->redirect_url;
    }
}
