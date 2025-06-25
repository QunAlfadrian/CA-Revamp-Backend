<?php

namespace Database\Seeders;

use App\Models\Book;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BooksTableSeeder extends Seeder {
    /**
     * Run the database seeds.
     */
    public function run(): void {
        $books = [
            'Penance',
            'Holy Mother',
            'Real Mother',
            'Real Face',
            'Confessions',
            'Girls',
            'Girls in the Dark',
            'The Dead Returns',
            'Giselle',
            'Cinderella Addiction',
            'Ferris Wheel at Night',
            'Masked Wards',
            'At Night I become a Monster',
            'Blue, Painful and Brittle',
            'Her Sunny Side',
            'Before the Coffee gets Cold',
            'Tales from the Cafe',
            'Before Your Memory Fades',
            'Before We Say Goodbye'
        ];

        foreach ($books as $title) {
            Book::create([
                'isbn' => fake()->isbn13(),
                'title' => $title,
                'synopsis' => fake()->paragraph(random_int(3, 6)),
                'author_1' => fake()->name(),
                'author_2' => random_int(0, 1) == 0 ? null : fake()->name(),
                'published_year' => fake()->year(),
                'cover_image_url' => 'https://images-na.ssl-images-amazon.com/images/S/compressed.photo.goodreads.com/books/1492877223i/28952640.jpg',
                'price' => random_int(60, 105) * 10000,
            ]);
        }
    }
}
