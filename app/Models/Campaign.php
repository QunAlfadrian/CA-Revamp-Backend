<?php

namespace App\Models;

use App\Traits\BelongsToManyBooks;
use App\Traits\HasManyDonations;
use App\Traits\HasManyFunds;
use App\Traits\HasOrganizer;
use App\Traits\HasReviewer;
use Illuminate\Support\Str;
use App\Traits\ModelHelpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model {
    use HasFactory;
    use ModelHelpers;

    /** Relation Helpers */
    use HasOrganizer;
    use HasReviewer;
    use HasManyFunds;
    use HasManyDonations;
    use BelongsToManyBooks;

    public $keyType = 'string';
    public $incrementing = 'false';

    public static function booted() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
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
        return (string)$this->id;
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

    public function headerImageUrl(): string {
        return $this->header_image_url;
    }

    public function status(): string {
        return $this->status;
    }

    public function createdAt(): string {
        return $this->created_at ? $this->created_at->format('d-m-Y H:i:s') : '';
    }
}
