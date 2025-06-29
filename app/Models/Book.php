<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Book extends Model {
    use HasFactory;

    public $keyType = 'string';
    public $incrementing = 'false';

    protected $fillable = [
        'isbn',
        'title',
        'synopsis',
        'author_1',
        'author_2',
        'author_3',
        'published_year',
        'cover_image_url',
        'price'
    ];

    protected function casts(): array {
        return [
            'created_at' => 'datetime'
        ];
    }

    public function id(): string {
        return $this->isbn;
    }

    public function isbn(): string {
        return $this->isbn;
    }

    public function title(): string {
        return $this->title;
    }

    public function synopsis(): string {
        return $this->synopsis;
    }

    public function authors(): array {
        return [
            'author_1' => $this->author_1,
            'author_2' => $this->author_2,
            'author_3' => $this->author_3,
        ];
    }

    public function publishedYear(): string {
        return $this->published_year;
    }

    public function coverImageUrl(): string {
        return $this->cover_image_url;
    }

    public function price(): string {
        return (string)$this->price;
    }
}
