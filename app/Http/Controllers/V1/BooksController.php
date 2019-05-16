<?php

namespace App\Http\Controllers\V1;

use App\Models\Book;
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
        $books = Book::sort($sort)->latest()->get();

        return $this->jsonResponse([
            'books' => $books
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
        $request->validate(
            [
                'author' => 'string'
            ],
            [
                'author.string' => '"author" should be a string'
            ]
        );

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
}
