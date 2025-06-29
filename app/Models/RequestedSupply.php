<?php

namespace App\Models;

use App\Traits\HasCampaign;
use Illuminate\Support\Str;
use App\Traits\ModelHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RequestedSupply extends Model {
    /** Model Helpers */
    use HasFactory;
    use ModelHelpers;

    /** Relationship Helpers */
    use HasCampaign;

    public $keyType = 'string';
    public $incrementing = 'false';

    public static function booted() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
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

    public function status(): string {
        return $this->status;
    }

    public function createdAt(): string {
        return $this->created_at ? $this->created_at->format('d-m-Y H-i-s') : '';
    }

    public function updatedAt(): string {
        return $this->updated_at ? $this->updated_at->format('d-m-Y H-i-s') : '';
    }
}
