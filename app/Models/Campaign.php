<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\HasReviewer;
use Illuminate\Support\Str;
use App\Traits\HasManyFunds;
use App\Traits\HasOrganizer;
use App\Traits\ModelHelpers;
use App\Traits\HasManyDonations;
use App\Traits\HasManyFacilities;
use App\Traits\BelongsToManyBooks;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasManyRequestedSupplies;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Campaign extends Model {
    use HasFactory;
    use SoftDeletes;
    use ModelHelpers;

    /** Relation Helpers */
    use HasOrganizer;
    use HasReviewer;
    use HasManyFunds;
    use HasManyDonations;
    use BelongsToManyBooks;
    use HasManyRequestedSupplies;
    // use HasManyFacilities;

    protected $primaryKey = 'campaign_id';
    public $keyType = 'string';
    public $incrementing = false;

    public static function booted() {
        parent::boot();

        static::creating(function ($model) {
            // $model->id = Str::uuid();
            $latestCampaign = Campaign::where('type', $model->type())
                ->orderBy('campaign_id', 'desc')
                ->first();

            if ($latestCampaign) {
                $latestDateCreated = Carbon::createFromFormat('ymd', substr($latestCampaign->id(), 5, 6));
                if ($latestDateCreated->isToday()) {
                    $latestID = intval(substr($latestCampaign->id(), -2));
                    $nextID = $latestID + 1;
                } else {
                    $nextID = 1;
                }
            } else {
                $nextID = 1;
            }

            $date = Carbon::today()->format('ymd');
            $type = $model->type() === 'fundraiser' ? 'F' : 'P';

            $model->campaign_id = 'CA_C' . $type . $date . sprintf("%02d", $nextID);
        });
    }

    protected $fillable = [
        'organizer_id',
        'type',
        'title',
        'slug',
        'description',
        'header_image_url',
        'status',
        'requested_fund_amount',
        'donated_fund_amount',
        'requested_item_quantity',
        'donated_item_quantity',
        'reviewed_by',
        'reviewed_at'
    ];

    protected function casts(): array {
        return [
            'created_at' => 'datetime',
            'reviewed_at' => 'datetime'
        ];
    }

    public function id(): string {
        return $this->campaign_id;
    }

    public function type(): string {
        return $this->type;
    }

    public function title(): string {
        return $this->title;
    }

    public function slug(): string {
        return $this->slug;
    }

    public function description(): string {
        return $this->description;
    }

    public function headerImageUrl(): string {
        return $this->header_image_url;
    }

    public function status(): string {
        return $this->status;
    }

    public function createdAt(): string {
        return $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : '';
    }

    public function requestedFund(): string {
        return $this->requested_fund_amount;
    }

    public function donatedFund(): string {
        return $this->donated_fund_amount;
    }

    public function requestedItemQuantity(): string {
        return $this->requested_item_quantity;
    }

    public function donatedItemQuantity(): string {
        return $this->donated_item_quantity;
    }
}
