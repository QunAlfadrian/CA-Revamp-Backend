<?php

namespace App\Models;

use App\FundStatus;
use App\Traits\HasCampaign;
use App\Traits\HasDonation;
use Illuminate\Support\Str;
use App\Traits\ModelHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Fund extends Model {
    use HasFactory;
    use ModelHelpers;

    /** Relation Helpers */
    use HasCampaign;
    use HasDonation;

    protected $primaryKey = 'fund_id';
    public $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'fund_id',
        'campaign_id',
        'donation_id',
        'amount',
        'service_fee',
        'status',
        'snap_token',
        'redirect_url'
    ];

    protected $casts = [
        'status' => FundStatus::class
    ];

    public function id(): string {
        return $this->fund_id;
    }

    public function orderId(): string {
        return $this->fund_id;
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
