<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class ExportBooksTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function exportingCsvFile()
    {
        $books = factory(Book::class, 2)->create();

        $expectedCsv = <<<TEXT
title,author
"{$books[0]->title}","{$books[0]->author}"
"{$books[1]->title}","{$books[1]->author}"

TEXT;

        $response = $this->get('api/v1/books/export/csv?fields[]=title&fields[]=author');

        $response->assertOk();
        $response->assertHeader('Content-disposition');
        $this->assertEquals($expectedCsv, $response->getContent());
    }

    /** @test */
    public function exportingCsvFileWithOnlyTitle()
    {
        $books = factory(Book::class, 2)->create();

        $expectedCsv = <<<TEXT
title
"{$books[0]->title}"
"{$books[1]->title}"

TEXT;

        $response = $this->get('api/v1/books/export/csv?fields[]=title');

        $response->assertOk();
        $response->assertHeader('Content-disposition');
        $this->assertEquals($expectedCsv, $response->getContent());
    }

    /** @test */
    public function exportingXmlFile()
    {
        $books = factory(Book::class, 2)->create();

        $expectedXml = <<<TEXT
<?xml version="1.0"?>
<root><book><title>{$books[0]->title}</title><author>{$books[0]->author}</author></book><book><title>{$books[1]->title}</title><author>{$books[1]->author}</author></book></root>

TEXT;

        $response = $this->get('api/v1/books/export/xml?fields[]=title&fields[]=author');

        $response->assertOk();
        $response->assertHeader('Content-disposition');
        $this->assertEquals($expectedXml, $response->getContent());
    }

    /** @test */
    public function exportingXmlFileWithOnlyAuthor()
    {
        $books = factory(Book::class, 2)->create();

        $expectedXml = <<<TEXT
<?xml version="1.0"?>
<root><book><author>{$books[0]->author}</author></book><book><author>{$books[1]->author}</author></book></root>

TEXT;

        $response = $this->get('api/v1/books/export/xml?fields[]=author');

        $response->assertOk();
        $response->assertHeader('Content-disposition');
        $this->assertEquals($expectedXml, $response->getContent());
    }

    /** @test */
    public function exportingXmlFileWithSortingAndFiltering()
    {
        $book1 = factory(Book::class)->create([
            'title' => 'aaaaaa 1'
        ]);

        $book2 = factory(Book::class)->create([
            'title' => 'aaaaaa 2'
        ]);

        factory(Book::class)->create([
            'title' => 'bbbbbb'
        ]);


        $expectedXml = <<<TEXT
<?xml version="1.0"?>
<root><book><title>{$book2->title}</title></book><book><title>{$book1->title}</title></book></root>

TEXT;

        $filters = [
            [
                'filters' => [
                    [
                        "field" => 'title',
                        "value" => 'aaaaaa',
                        "operator" => 'like'
                    ]
                ]
            ]
        ];

        $filters = $this->formFilteringQueryParams($filters);

        $uri = 'api/v1/books/export/xml?' . $filters . 'fields[]=title&sort[]={"field":"title","direction":"desc"}';
        $response = $this->get($uri);

        $response->assertOk();
        $response->assertHeader('Content-disposition');
        $this->assertEquals($expectedXml, $response->getContent());
    }

    /** @test */
    public function exportingCsvFileWithSortingAndFiltering()
    {
        $book1 = factory(Book::class)->create([
            'title' => 'aaaaaa 1'
        ]);

        $book2 = factory(Book::class)->create([
            'title' => 'aaaaaa 2'
        ]);

        factory(Book::class)->create([
            'title' => 'bbbbbb'
        ]);


        $expectedCsv = <<<TEXT
title
"{$book2->title}"
"{$book1->title}"

TEXT;

        $filters = [
            [
                'filters' => [
                    [
                        "field" => 'title',
                        "value" => 'aaaaaa',
                        "operator" => 'like'
                    ]
                ]
            ]
        ];

        $filters = $this->formFilteringQueryParams($filters);

        $uri = 'api/v1/books/export/csv?' . $filters . 'fields[]=title&sort[]={"field":"title","direction":"desc"}';
        $response = $this->get($uri);

        $response->assertOk();
        $response->assertHeader('Content-disposition');
        $this->assertEquals($expectedCsv, $response->getContent());
    }
}
