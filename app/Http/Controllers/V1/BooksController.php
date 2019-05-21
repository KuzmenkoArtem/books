<?php

namespace App\Http\Controllers\V1;

use App\Models\Book;
use App\Services\Export\ExporterFactory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller as BaseController;

class BooksController extends BaseController
{
    /**
     * Get a list of books applying sorting on demand.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request)
    {
        $sort = $request->get('sort');
        $filterGroups = $request->get('filter_groups');
        $books = Book::filter($filterGroups)->sort($sort)->latest()->get();

        return $this->jsonResponse([
            'books' => $books
        ]);
    }

    /**
     * Store a new book in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'author' => 'required|string'
        ]);

        $book = new Book;
        $book->title = $request->title;
        $book->author = $request->author;
        $book->save();
    }

    /**
     * Display the specified book.
     *
     * @param  \App\Models\Book  $book
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSpecific(Book $book)
    {
        return $this->jsonResponse($book);
    }

    /**
     * Update the specified book in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Book  $book
     */
    public function update(Request $request, Book $book)
    {
        $request->validate([
            'author' => 'string'
        ]);

        $book->update($request->all());
    }

    /**
     * Remove the specified book from storage.
     *
     * @param \App\Models\Book $book
     * @throws \Exception
     */
    public function destroy(Book $book)
    {
        $book->delete();
    }

    /**
     * Remove the specified book from storage.
     *
     * @param Request $request
     * @param string $type
     * @return \Illuminate\Http\Response
     * @throws \App\Exceptions\WrongExportingFormat
     */
    public function export(Request $request, string $type)
    {
        $sort = $request->get('sort');
        $filterGroups = $request->get('filter_groups');
        $fields = $request->get('fields', []);

        $books = Book::filter($filterGroups)->sort($sort)->latest()->get();
        $exporter = ExporterFactory::getExporter($type);
        $file = $exporter->setCollection($books)->setFields($fields)->setFileName('books')->getFile();

        return $this->streamDownloadFile($file);
    }
}
