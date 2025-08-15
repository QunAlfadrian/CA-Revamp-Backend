<?php

namespace App\Models;

use App\Traits\HasCampaign;
use App\Traits\HasDonation;
use App\Traits\HasDonor;
use App\Traits\HasManyDonatedItems;
use App\Traits\HasManyFunds;
use App\Traits\ModelHelpers;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model {
    /** Model Helpers */
    use ModelHelpers;

    /** Relation Helpers */
    use HasManyFunds;
    use HasManyDonatedItems;
    use HasDonor;
    use HasCampaign;

    protected $primaryKey = 'donation_id';
    public $keyType = 'string';
    public $incrementing = false;

    public static function booted() {
        parent::boot();

        static::creating(function ($model) {
            $campaign = $model->campaign();
            $latestDonation = $campaign
                ->donationsRelation()
                ->orderBy('donation_id', 'desc')
                ->first();

            if ($latestDonation) {
                $latestID = hexdec(substr($campaign->id(), -4));
                $nextID = $latestID + 1;
            } else {
                $nextID = 1;
            }

            $model->donation_id = $campaign->id() . 'D' . sprintf("%05X", $nextID);
        });
    }

    protected $fillable = [
        'donor_id',
        'donor_name',
        'campaign_id',
        'type',
        'verified_at'
    ];

    public function casts(): array {
        return [
            'verified_at' => 'datetime'
        ];
    }

    public function id(): string {
        return (string)$this->donationd_id;
    }

    public function donorName(): ?string {
        return $this->donor_name;
    }

    public function type(): string {
        return $this->type;
    }

    public function verifiedAt(): string {
        return $this->verified_at ? $this->verified_at : '';
    }
}
