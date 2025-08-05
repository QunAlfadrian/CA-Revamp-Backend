<?php

namespace App\Models;

use App\Traits\HasCampaign;
use Illuminate\Support\Str;
use App\Traits\ModelHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Facility extends Model {
    use HasFactory;
    use ModelHelpers;

    /** Relation Helpers */
    use HasCampaign;

    public $keyType = 'string';
    public $incrementing = false;

    public static function booted() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    protected $fillable = [
        'name',
        'description',
        'requested_quantity',
        'donated_quantity',
    ];

    public function id(): string {
        return $this->id;
    }

    public function name(): string {
        return $this->name;
    }

    public function slug(): string {
        return $this->slug;
    }

    public function description(): string {
        return $this->description;
    }

    public function requestedQuantity(): string {
        return $this->requested_quantity;
    }

    public function donatedQuantity(): string {
        return $this->donated_quantity;
    }
}
