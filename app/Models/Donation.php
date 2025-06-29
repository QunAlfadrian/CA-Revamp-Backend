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

    public $keyType = 'string';
    public $incrementing = 'false';

    public static function booted() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    protected $fillable = [
        'donor_id',
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
        return (string)$this->id;
    }

    public function type(): string {
        return $this->type;
    }

    public function verifiedAt(): string {
        return $this->verified_at ? $this->verified_at->format('d-m-Y H:i:s') : '';
    }
}
