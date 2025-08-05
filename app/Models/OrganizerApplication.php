<?php

namespace App\Models;

use App\Traits\HasApplicant;
use App\Traits\HasDonor;
use App\Traits\HasReviewer;
use Illuminate\Support\Str;
use App\Traits\ModelHelpers;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrganizerApplication extends Model {
    /** Model Helpers */
    use HasFactory;
    use ModelHelpers;

    /** Relationship Helpers */
    use HasApplicant;
    use HasReviewer;

    public $keyType = 'string';
    public $incrementing = false;

    public static function booted() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    protected $fillable = [
        'user_id',
        'status',
        'rejected_message',
        'reviewed_by',
        'reviewed_at'
    ];

    public function casts() {
        return [
            'reviewed_at' => 'datetime'
        ];
    }

    public function id(): string {
        return $this->id;
    }

    public function status(): string {
        return $this->attributes['status'];
    }

    public function rejectedMessage(): ?string {
        return $this->rejected_message;
    }
}
