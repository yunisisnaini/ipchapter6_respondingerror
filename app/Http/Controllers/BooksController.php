<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use App\Models\Book;

class BooksController extends Controller
{
    /**
     * Create a new controller instance.
     * 
     * @return array
     */
    public function index()
    {
        return Book::all()->toArray();
    }

    public function show($id)
    {
        try {
            return Book::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => [
                    'message' => 'Book not found'
                ]
            ], 404);
        }

        // return Book::findOrFail($id);
    }

    public function store(Request $request)
    {
        $book = Book::create($request->all());
        return response()->json(['created' => true], 201, [
            'Location' => route('books.show', ['id' => $book->id])
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $book = Book::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => [
                    'message' => 'Book not found'
                ]
            ], 404);
        }

        $book->fill($request->all());
        $book->save();

        return $book;
    }

    public function destroy($id)
    {
        try {
            $book = Book::findOrFail($id);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'error' => [
                    'message' => 'Book not found'
                ]
            ], 404);
        }

        $book->delete();
        return response(null, 204);
    }
}