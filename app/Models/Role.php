<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Traits\ModelHelpers;
use App\Traits\BelongsToManyUsers;
use Illuminate\Database\Eloquent\Model;

class Role extends Model {
    use ModelHelpers;
    use BelongsToManyUsers;

    public $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'name'
    ];

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
