<?php

namespace App\Models;

use App\Traits\HasCampaign;
use App\Traits\HasDonation;
use App\Traits\HasReviewer;
use Illuminate\Support\Str;
use App\Traits\ModelHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DonatedItem extends Model {
    /** Model Helpers */
    use HasFactory;
    use ModelHelpers;

    /** Relationship Helpers */
    use HasCampaign;
    use HasDonation;
    use HasReviewer;

    public $keyType = 'string';
    public $incrementing = 'false';

    public static function booted() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    protected $fillable = [
        'campaign_id',
        'donor_id',
        'donated_item_quantity',
        'package_picture_url',
        'delivery_service',
        'resi',
        'status'
    ];

    public function id(): string {
        return $this->id;
    }

    public function donatedItemQuantity(): string {
        return (string)$this->donated_item_quantity;
    }

    public function packagePictureUrl(): string {
        return $this->package_picture_url;
    }

    public function deliveryService(): string {
        return $this->delivery_service;
    }

    public function resi(): string {
        return $this->resi;
    }

    public function status(): string {
        return $this->status;
    }

    public function createdAt(): string {
        return $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : '';
    }

    public function updatedAt(): string {
        return $this->updated_at ? $this->updated_at->format('d-m-Y H:i:s') : '';
    }
}
