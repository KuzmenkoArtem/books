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
        factory(Book::class, 10)->create();

        $response = $this->get('api/v1/books?sort[]={"field":"nonexistent_field","direction":"desc"}');

        $response->assertStatus(422);
    }

    /** @test */
    public function gettingAllBooksWithSortingWithBrokenStructure()
    {
        factory(Book::class, 10)->create();

        $response = $this->get('api/v1/books?sort[]={"broken_field":"nonexistent_field","broken_direction":"desc"}');

        $response->assertStatus(422);
    }

    /** @test */
    public function gettingAllBooksWithSortingWithBrokenDirection()
    {
        factory(Book::class, 10)->create();

        $response = $this->get('api/v1/books?sort[]={"field":"title","direction":"wrong"}');

        $response->assertStatus(422);
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

    /** @test */
    public function deletingBook()
    {
        $book = factory(Book::class)->create();

        $response = $this->delete("api/v1/books/{$book->id}");

        $response->assertOk();
        $this->assertDatabaseMissing('books', [
            'id' => $book->id
        ]);
    }

    /** @test */
    public function deletingNonexistentBook()
    {
        factory(Book::class)->create();

        $response = $this->delete("api/v1/books/999");

        $response->assertNotFound();
    }

    /** @test */
    public function updatingBook()
    {
        $book = factory(Book::class)->create();
        $newAuthor = 'Jon Snow';

        $response = $this->put("api/v1/books/{$book->id}", ['author' => $newAuthor]);

        $response->assertOk();
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'author' => $newAuthor
        ]);
    }

    /** @test */
    public function updatingNonexistentBook()
    {
        factory(Book::class)->create();
        $newAuthor = 'Jon Snow';

        $response = $this->put("api/v1/books/999", ['author' => $newAuthor]);

        $response->assertNotFound();
    }

    /** @test */
    public function updatingBookWithNotAllowedField()
    {
        $book = factory(Book::class)->create();
        $newTitle = 'Anna Karenina';

        $response = $this->put("api/v1/books/{$book->id}", ['title' => $newTitle]);

        $response->assertOk();
        $this->assertDatabaseHas('books', [
            'id' => $book->id,
            'title' => $book->title
        ]);
    }

    /** @test */
    public function updatingBookWithNonexistentField()
    {
        $book = factory(Book::class)->create();

        $response = $this->put("api/v1/books/{$book->id}", ['nonexistent_field' => 'value']);

        $response->assertOk();
    }

    /** @test */
    public function updatingBookWithWrongFormat()
    {
        $book = factory(Book::class)->create();
        $newAuthor = 'Fyodor Dostoevsky';

        $headers = [
            "Accept" => 'application/json'
        ];

        $wrongFormat = ['author' => [$newAuthor]];

        $response = $this->put("api/v1/books/{$book->id}", $wrongFormat, $headers);

        $response->assertStatus(422);
        $response->assertJsonStructure([
            'errors' => [
                'author'
            ]
        ]);
    }

    /** @test */
    public function creatingBook()
    {
        $data = [
            'title' => 'Harry Potter',
            'author' => 'Joanne Rowling'
        ];

        $response = $this->post("api/v1/books", $data);

        $response->assertOk();
        $this->assertDatabaseHas('books', $data);
    }

    /** @test */
    public function creatingBookWithMissingFields()
    {
        $headers = [
            "Accept" => 'application/json'
        ];

        $response = $this->post("api/v1/books", [], $headers);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'errors' => [
                'title',
                'author'
            ]
        ]);
    }

    /** @test */
    public function creatingBookWithWrongFormat()
    {
        $headers = [
            "Accept" => 'application/json'
        ];

        $data = [
            'title' => ['Harry Potter'],
            'author' => ['Joanne Rowling']
        ];

        $response = $this->post("api/v1/books", $data, $headers);
        $response->assertStatus(422);
        $response->assertJsonStructure([
            'errors' => [
                'title',
                'author'
            ]
        ]);
    }
}
