<?php

namespace App\Models;

use App\Traits\BelongsToBook;
use App\Traits\HasDonation;
use App\Traits\ModelHelpers;
use Illuminate\Database\Eloquent\Relations\Pivot;

class DonatedBook extends Pivot {
    // model helper
    use ModelHelpers;

    // relationship helper
    use BelongsToBook;

    public $keyType = 'string';
    public $incrementing = false;
    protected $table = 'donated_books';

    protected $fillable = [
        'quantity'
    ];

    public function donationId(): string {
        return $this->donation_id;
    }

    public function bookId(): string {
        return $this->book_id;
    }

    public function quantity(): string {
        return (string) $this->quantity;
    }
}
