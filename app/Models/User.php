<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\HasIdentity;
use Illuminate\Support\Str;
use App\Traits\ModelHelpers;
use App\Traits\HasManyCampaigns;
use App\Traits\HasManyDonations;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\BelongsToManyRoles;
use App\Traits\HasOrganizerApplication;
use App\Traits\ReviewManyCampaigns;
use Illuminate\Notifications\Notifiable;
use App\Traits\ReviewManyOrganizerApplications;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
	/** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens;
	use HasFactory;
	use Notifiable;
    use ModelHelpers;

    /** Relation Helpers */
    use BelongsToManyRoles;

    /** Admin Relation */
    use ReviewManyCampaigns;
    use ReviewManyOrganizerApplications;

    /** Organizer Relation */
    use HasManyCampaigns;

    /** Donor Relation */
    use HasManyDonations;
    use HasIdentity;
    use HasOrganizerApplication;

    public $keyType = 'string';
    public $incrementing = 'false';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var list<string>
	 */
	protected $fillable = [
		'name',
		'email',
		'password',
	];

	/**
	 * The attributes that should be hidden for serialization.
	 *
	 * @var list<string>
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	/**
	 * Get the attributes that should be cast.
	 *
	 * @return array<string, string>
	 */
	protected function casts(): array {
		return [
			'email_verified_at' => 'datetime',
			'password' => 'hashed',
		];
	}

    public static function booted() {
        parent::boot();

        static::creating(function ($model) {
            $model->id = Str::uuid();
        });
    }

    public function id(): string {
        return (string)$this->id;
    }

    public function name(): string {
        return $this->name;
    }

    public function email(): string {
        return $this->email;
    }

    public function emailVerifiedAt(): string {
        return $this->email_verified_at ? $this->email_verified_at->format('d-m-Y') : '';
    }
}
