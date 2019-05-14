<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class BooksTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function gettingAllBooks()
    {
        $books = factory(Book::class, 10)->create();

        $response = $this->get('api/v1/books');

        $response->assertJson([
            'books' => $books->toArray()
        ]);
    }
}
