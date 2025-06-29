<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Traits\ModelHelpers;
use Illuminate\Database\Eloquent\Model;

class Identity extends Model {
    use ModelHelpers;

    public $keyType = 'string';
    public $incrementing = 'false';

    public static function booted() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    protected $fillable = [
        'full_name',
        'phone_number',
        'gender',
        'profile_image_url',
        'date_of_birth',
        'nik',
        'id_card_image_url'
    ];

    protected function casts(): array {
        return [
            'date_of_birth' => 'date',
            'created_at' => 'datetime',
        ];
    }

    public function id(): string {
        return (string)$this->id;
    }

    public function fullName(): string {
        return $this->fullName;
    }

    public function phoneNumber(): string {
        return $this->phone_number ? $this->phone_number : '';
    }

    public function gender(): string {
        return $this->gender ? $this->gender : '';
    }

    public function dateOfBirth(): string {
        return $this->date_of_birth ? $this->date_of_birth->format('d-m-Y') : '';
    }

    public function nik(): string {
        return $this->nik ? $this->nik : '';
    }

    public function idCardImageUrl(): string {
        return $this->id_card_image_url ? $this->id_card_image_url : '';
    }
}
