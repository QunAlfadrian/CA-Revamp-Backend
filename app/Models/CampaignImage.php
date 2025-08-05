<?php

namespace App\Models;

use App\Traits\HasCampaign;
use Illuminate\Support\Str;
use App\Traits\ModelHelpers;
use Illuminate\Database\Eloquent\Model;

class CampaignImage extends Model {
    use ModelHelpers;
    use HasCampaign;

    public $keyType = 'string';
    public $incrementing = false;

    public static function booted() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function id(): string {
        return $this->id;
    }

    public function name(): string {
        return $this->name;
    }

    public function slug(): string {
        return $this->slug;
    }

    public function alternativeText(): string {
        return $this->alternative_text;
    }

    public function filename(): string {
        return $this->filename;
    }

    public function url(): string {
        return $this->url;
    }

    public function createdAt(): string {
        return $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : '';
    }
}
