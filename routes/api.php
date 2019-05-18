<?php

Route::prefix('v1')->group(function () {
    Route::get('/books', 'V1\BooksController@get');
    Route::get('/books/{book}', 'V1\BooksController@getSpecific');
    Route::post('/books/', 'V1\BooksController@store');
    Route::delete('/books/{book}', 'V1\BooksController@destroy');
    Route::put('/books/{book}', 'V1\BooksController@update');
});
