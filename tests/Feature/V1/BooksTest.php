<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Arr;
use Tests\TestCase;

class BooksTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function gettingAllBooks()
    {
        $books = factory(Book::class, 10)->create();

        $response = $this->get('api/v1/books');

        $response->assertExactJson([
            'books' => $books->toArray()
        ]);
    }

    /** @test */
    public function gettingAllBooksWithSortingByTitleByAsc()
    {
        $books = factory(Book::class, 10)->create();

        $books = $books->sortBy('title');

        $response = $this->get('api/v1/books?sort[]={"field":"title","direction":"asc"}');
        $booksInResponse = $response->json('books');

        $expected = $books->pluck('id')->toArray();
        $actual = Arr::pluck($booksInResponse, 'id');
        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function gettingAllBooksWithSortingByTitleByDesc()
    {
        $books = factory(Book::class, 10)->create();

        $books = $books->sortByDesc('title');

        $response = $this->get('api/v1/books?sort[]={"field":"title","direction":"desc"}');
        $booksInResponse = $response->json('books');

        $expected = $books->pluck('id')->toArray();
        $actual = Arr::pluck($booksInResponse, 'id');
        $this->assertEquals($expected, $actual);
    }

    /** @test */
    public function gettingAllBooksWithSortingByNonexistentField()
    {
        $books = factory(Book::class, 10)->create();

        $response = $this->get('api/v1/books?sort[]={"field":"nonexistent_field","direction":"desc"}');

        $response->assertExactJson([
            'books' => $books->toArray()
        ]);
    }

    /** @test */
    public function gettingAllBooksWithSortingWithBrokenStructure()
    {
        $books = factory(Book::class, 10)->create();

        $response = $this->get('api/v1/books?sort[]={"broken_field":"nonexistent_field","broken_direction":"desc"}');

        $response->assertExactJson([
            'books' => $books->toArray()
        ]);
    }

    /** @test */
    public function gettingAllBooksWithSortingWithBrokenDirection()
    {
        $books = factory(Book::class, 10)->create();

        $response = $this->get('api/v1/books?sort[]={"field":"title","direction":"wrong"}');

        $response->assertExactJson([
            'books' => $books->toArray()
        ]);
    }

    /** @test */
    public function gettingAllBooksWithSortingByCombinedFields()
    {
        $booksAA = factory(Book::class, 1)->create(['title' => 'A', 'author' => 'A']);
        $booksAB = factory(Book::class, 2)->create(['title' => 'A', 'author' => 'B']);
        $booksBA = factory(Book::class, 1)->create(['title' => 'B', 'author' => 'A']);
        $booksBB = factory(Book::class, 2)->create(['title' => 'B', 'author' => 'B']);

        $books = $booksBB->merge($booksBA)->merge($booksAB)->merge($booksAA);

        $uri = 'api/v1/books?sort[]={"field":"title","direction":"desc"}&sort[]={"field":"author","direction":"desc"}';
        $response = $this->get($uri);
        $booksInResponse = $response->json('books');

        $expected = $books->pluck('id')->toArray();
        $actual = Arr::pluck($booksInResponse, 'id');
        $this->assertEquals($expected, $actual);
    }
}
