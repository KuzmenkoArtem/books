<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class FiltersTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function filteringBooksByTitle()
    {
        factory(Book::class, 10)->create();
        $time = time();
        $annaKarenina = factory(Book::class, 1)->create([
            'title' => "Jon Snow" . $time . "Anna Karenina",
        ]);

        $books = factory(Book::class, 1)->create([
            'title' => "Some salt" . $time . "Lorem Ipsum",
        ]);

        $books = $books->merge($annaKarenina);

        $params = [
            [
                'filters' => [
                    [
                        "field" => "title",
                        "value" => $time,
                        "operator" => "like"
                    ]
                ]
            ]
        ];
        $params = $this->formFilteringQueryParams($params);
        $response = $this->get("api/v1/books?{$params}");

        $response->assertOk();
        $response->assertExactJson([
            'books' => $books->toArray()
        ]);
    }

    /** @test */
    public function filteringBooksByAuthor()
    {
        factory(Book::class, 10)->create();
        $time = time();
        $annaKarenina = factory(Book::class, 1)->create([
            'author' => "Jon Snow" . $time . "Anna Karenina",
        ]);

        $books = factory(Book::class, 1)->create([
            'author' => "Some salt" . $time . "Lorem Ipsum",
        ]);

        $books = $books->merge($annaKarenina);

        $params = [
            [
                'filters' => [
                    [
                        "field" => "author",
                        "value" => $time,
                        "operator" => "like"
                    ]
                ]
            ]
        ];
        $params = $this->formFilteringQueryParams($params);

        $response = $this->get("api/v1/books?{$params}");

        $response->assertOk();
        $response->assertExactJson([
            'books' => $books->toArray()
        ]);
    }

    /** @test */
    public function filteringBooksByAuthorAndTitle()
    {
        factory(Book::class, 10)->create();
        $time = time();
        factory(Book::class)->create([
            'author' => "Jon Snow" . $time . "Anna Karenina",
        ]);

        $title = "Super title";
        $books = factory(Book::class, 1)->create([
            'title' => $title,
            'author' => "Some salt" . $time . "Lorem Ipsum",
        ]);

        $params = [
            [
                'or' => false,
                'filters' => [
                    [
                        "or" => false,
                        "field" => "author",
                        "value" => $time,
                        "operator" => "like"
                    ],
                    [
                        "or" => false,
                        "field" => "title",
                        "value" => $title,
                        "operator" => "like"
                    ]
                ]
            ]
        ];
        $params = $this->formFilteringQueryParams($params);
        $response = $this->get("api/v1/books?{$params}");

        $response->assertOk();
        $response->assertExactJson([
            'books' => $books->toArray()
        ]);
    }

    /** @test */
    public function filteringBooksByAuthorOrTitle()
    {
        factory(Book::class, 10)->create();
        $time = time();
        $booksMerged = factory(Book::class, 1)->create([
            'author' => "Jon Snow" . $time . "Anna Karenina",
        ]);

        $books = factory(Book::class, 1)->create([
            'title' => "Jon Snow" . $time . "Anna Karenina",
        ]);

        $books = $books->merge($booksMerged);

        $params = [
            [
                'or' => false,
                'filters' => [
                    [
                        "or" => false,
                        "field" => "author",
                        "value" => $time,
                        "operator" => "like"
                    ],
                    [
                        "or" => true,
                        "field" => "title",
                        "value" => $time,
                        "operator" => "like"
                    ]
                ]
            ]
        ];
        $params = $this->formFilteringQueryParams($params);
        $response = $this->get("api/v1/books?{$params}");

        $response->assertOk();
        $response->assertExactJson([
            'books' => $books->toArray()
        ]);
    }

    /** @test */
    public function filteringBooksUsingGroups()
    {
        factory(Book::class, 10)->create();
        $time = time();
        $booksMerged = factory(Book::class, 1)->create([
            'author' => "Jon Snow" . $time . "Anna Karenina",
        ]);

        $books = factory(Book::class, 1)->create([
            'title' => "Jon Snow" . $time . "Anna Karenina",
        ]);

        $books = $books->merge($booksMerged);

        $params = [
            [
                'filters' => [
                    [
                        "or" => false,
                        "field" => "author",
                        "value" => $time,
                        "operator" => "like"
                    ]
                ]
            ],
            [
                'or' => true,
                'filters' => [
                    [
                        "or" => false,
                        "field" => "title",
                        "value" => $time,
                        "operator" => "like"
                    ]
                ]
            ]
        ];
        $params = $this->formFilteringQueryParams($params);
        $response = $this->get("api/v1/books?{$params}");

        $response->assertOk();
        $response->assertExactJson([
            'books' => $books->toArray()
        ]);
    }

    /** @test */
    public function filteringBooksWithMissingField()
    {
        factory(Book::class, 10)->create();

        $params = [
            [
                'filters' => [
                    [
                        "or" => false,
                        "value" => 'value',
                        "operator" => "like"
                    ]
                ]
            ]
        ];

        $params = $this->formFilteringQueryParams($params);
        $response = $this->get("api/v1/books?{$params}");

        $response->assertStatus(422);
    }

    /** @test */
    public function filteringBooksWithMissingValue()
    {
        factory(Book::class, 10)->create();

        $params = [
            [
                'filters' => [
                    [
                        "or" => false,
                        "field" => 'title',
                        "operator" => "like"
                    ]
                ]
            ]
        ];

        $params = $this->formFilteringQueryParams($params);
        $response = $this->get("api/v1/books?{$params}");

        $response->assertStatus(422);
    }

    /** @test */
    public function filteringBooksWithMissingOperator()
    {
        factory(Book::class, 10)->create();

        $params = [
            [
                'filters' => [
                    [
                        "or" => false,
                        "field" => 'title',
                        "value" => 'value',
                    ]
                ]
            ]
        ];

        $params = $this->formFilteringQueryParams($params);
        $response = $this->get("api/v1/books?{$params}");

        $response->assertStatus(422);
    }

    /** @test */
    public function filteringBooksWithUnAvailableField()
    {
        factory(Book::class, 10)->create();

        $params = [
            [
                'filters' => [
                    [
                        "or" => false,
                        "field" => 'created_at',
                        "value" => 'value',
                        "operator" => "like"
                    ]
                ]
            ]
        ];

        $params = $this->formFilteringQueryParams($params);
        $response = $this->get("api/v1/books?{$params}");

        $response->assertStatus(422);
    }

    /** @test */
    public function filteringBooksWithBrokenFieldData()
    {
        factory(Book::class, 10)->create();

        $params = [
            [
                'filters' => [
                    [
                        "or" => false,
                        "field" => ['title'],
                        "value" => 'value',
                        "operator" => "like"
                    ]
                ]
            ]
        ];

        $params = $this->formFilteringQueryParams($params);
        $response = $this->get("api/v1/books?{$params}");

        $response->assertStatus(422);
    }

    /** @test */
    public function filteringBooksWithBrokenValueData()
    {
        factory(Book::class, 10)->create();

        $params = [
            [
                'filters' => [
                    [
                        "or" => false,
                        "field" => 'title',
                        "value" => ['value'],
                        "operator" => "like"
                    ]
                ]
            ]
        ];

        $params = $this->formFilteringQueryParams($params);
        $response = $this->get("api/v1/books?{$params}");

        $response->assertStatus(422);
    }

    /** @test */
    public function filteringBooksWithBrokenOperatorData()
    {
        factory(Book::class, 10)->create();

        $params = [
            [
                'filters' => [
                    [
                        "or" => false,
                        "field" => 'title',
                        "value" => 'value',
                        "operator" => ['like']
                    ]
                ]
            ]
        ];

        $params = $this->formFilteringQueryParams($params);
        $response = $this->get("api/v1/books?{$params}");

        $response->assertStatus(422);
    }

    /** @test */
    public function filteringBooksWithUnAvailableOperator()
    {
        factory(Book::class, 10)->create();

        $params = [
            [
                'filters' => [
                    [
                        "or" => false,
                        "field" => 'title',
                        "value" => 'value',
                        "operator" => 'equals'
                    ]
                ]
            ]
        ];

        $params = $this->formFilteringQueryParams($params);
        $response = $this->get("api/v1/books?{$params}");

        $response->assertStatus(422);
    }

    /** @test */
    public function filteringBooksWithFilterKeyWrongStructure()
    {
        factory(Book::class, 10)->create();

        $params = [
            [
                'filters' => 'test'
            ]
        ];

        $params = $this->formFilteringQueryParams($params);
        $response = $this->get("api/v1/books?{$params}");

        $response->assertStatus(422);
    }
}
