<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\HasCampaign;
use App\Traits\HasReviewer;
use Illuminate\Support\Str;
use App\Traits\ModelHelpers;
use App\Traits\RequestedSupplyBelongsToManyDonatedItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestedSupply extends Model {
    /** Model Helpers */
    use HasFactory;
    use ModelHelpers;

    /** Relationship Helpers */
    use HasCampaign;
    use HasReviewer;
    use RequestedSupplyBelongsToManyDonatedItem;

    protected $primaryKey = 'requested_supply_id';
    public $keyType = 'string';
    public $incrementing = false;

    public static function booted() {
        parent::boot();

        static::creating(function ($model) {
            $latestModel = self::orderBy('created_at', 'desc')->first();

            if ($latestModel) {
                $latestDateCreated = Carbon::createFromFormat('ymd', substr($latestModel->id(), 6, 6));
                if ($latestDateCreated->isToday()) {
                    $latestID = hexdec(substr($latestModel->id(), -4));
                    $nextID = $latestID + 1;
                } else {
                    $nextID = 1;
                }
            } else {
                $nextID = 1;
            }

            $date = Carbon::today()->format('ymd');
            $model->requested_supply_id = 'CA_SUP' . $date . sprintf("%04X", $nextID);
        });
    }

    protected $fillable = [
        'name',
        'description',
        'price',
        'requested_quantity',
        'donated_quantity',
        'status',
        'campaign_id',
        'last_reviewed_by',
        'last_reviewed_at'
    ];

    public function id(): string {
        return $this->requested_supply_id;
    }

    public function name(): string {
        return $this->name;
    }

    public function description(): string {
        return $this->description;
    }

    public function price(): string {
        return (string)$this->price;
    }

    public function requestedQuantity(): string {
        return (string)$this->requested_quantity;
    }

    public function donatedQuantity(): string {
        return (string)$this->donated_quantity;
    }

    public function createdAt(): string {
        return $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : '';
    }

    public function updatedAt(): string {
        return $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : '';
    }
}
