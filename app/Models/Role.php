<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Traits\ModelHelpers;
use App\Traits\BelongsToManyUsers;
use Illuminate\Database\Eloquent\Model;

class Role extends Model {
    use ModelHelpers;
    use BelongsToManyUsers;

    protected $primaryKey = 'role_id';
    public $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name'
    ];

    public static function booted() {
        parent::boot();

        static::creating(function ($model) {
            // $model->id = Str::uuid();
            $latestRole = self::orderBy('role_id', 'desc')->first();

            if ($latestRole) {
                $latestID = intval(substr($latestRole->id(), 4));
                $nextID = $latestID++;
            } else {
                $nextID = 1;
            }

            $model->role_id = 'CA_R' . $nextID;
            while (self::where('role_id', $model->role_id)->exists()) {
                $nextID++;
                $model->role_id = 'CA_R' . $nextID;
            }
        });
    }

    public function id(): string {
        return $this->role_id;
    }

    public function name(): string {
        return $this->name;
    }

    public static function superAdmin() {
        return self::where('name', 'super_admin')->first();
    }

    public static function admin() {
        return self::where('name', 'admin')->first();
    }

    public static function donor() {
        return self::where('name', 'donor')->first();
    }

    public static function organizer() {
        return self::where('name', 'organizer')->first();
    }
}
