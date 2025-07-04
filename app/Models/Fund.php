<?php

namespace App\Models;

use App\Traits\HasCampaign;
use App\Traits\HasDonation;
use App\Traits\ModelHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fund extends Model {
    use HasFactory;
    use ModelHelpers;

    /** Relation Helpers */
    use HasCampaign;
    use HasDonation;

    public $keyType = 'string';
    public $incrementing = 'false';

    protected $fillable = [
        'id',
        'campaign_id',
        'donation_id',
        'amount',
        'status',
        'snap_token',
        'redirect_url'
    ];

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
