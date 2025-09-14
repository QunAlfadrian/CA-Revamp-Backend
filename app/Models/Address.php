<?php

namespace App\Models;

use App\Traits\ModelHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model {
    use HasFactory;
    use ModelHelpers;

    /** Relation Helpers */

    protected $primaryKey = 'campaign_id';
    public $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'campaign_id',
        'address_detail',
        'village',
        'sub_district',
        'city',
        'province',
        'postal_code',
    ];

    protected function casts(): array {
        return [
            'created_at' => 'datetime'
        ];
    }

    public function id(): string {
        return $this->campaign_id;
    }

    public function addressDetail(): string{
        return $this->address_detail;
    }

    public function village(): string {
        return $this->village;
    }

    public function subDistrict(): string {
        return $this->sub_district;
    }

    public function city(): string {
        return $this->city;
    }

    public function province(): string {
        return $this->province;
    }
}
