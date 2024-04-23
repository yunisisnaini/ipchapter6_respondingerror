<?php

namespace tests\app\Http\Controllers;

use App\Models\Book;
use Tests\TestCase;
use Database\Factories\ModelFactory;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Lumen\Testing\DatabaseTransactions;

class BooksControllerTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_that_base_endpoint_returns_a_successful_response()
    {
        $this->get('/');

        $this->assertEquals(
            $this->app->version(),
            $this->response->getContent()
        );
    }

    /** @test **/
    public function index_status_code_should_be_200()
    {
        $this->get('/books')->seeStatusCode(200);
    }

    /** @test **/
    public function index_should_return_a_collection_of_records()
    {
        $books = ModelFactory::new()->count(2)->create();
        $this->get('/books');
        foreach ($books as $book) {
            $this->seeJson(['title' => $book->title]);
        }
    }

    /** @test **/
    public function show_should_return_a_valid_book()
    {
        $book = ModelFactory::new()->create();
        $this
            ->get("/books/{$book->id}")
            ->seeStatusCode(200)
            ->seeJson([
                'id' => $book->id,
                'title' => $book->title,
                'description' => $book->description,
                'author' => $book->author
            ]);
        $data = json_decode($this->response->getContent(), true);
        $this->assertArrayHasKey('created_at', $data);
        $this->assertArrayHasKey('updated_at', $data);
    }

    /** @test **/

    /** @test **/
    public function show_route_should_not_match_an_invalid_route()
    {
        $this->get('/books/this-is-invalid');
        $this->assertDoesNotMatchRegularExpression(
            '/Book not found/',
            $this->response->getContent(),
            'BooksController@show route matching when it should not.'
        );
    }

    /** @test **/
    public function store_should_save_new_book_in_the_database()
    {
        $this->post('/books', [
            'title' => 'The Invisible Man',
            'description' => 'An invisible man is trapped in the terror of his own creation',
            'author' => 'H. G. Wells'
        ]);
        $this
            ->seeJson(['created' => true])
            ->seeInDatabase('books', ['title' => 'The Invisible Man']);
    }

    /** @test */
    public function store_should_respond_with_a_201_and_location_header_when_successful()
    {
        $this->post('/books', [
            'title' => 'The Invisible Man',
            'description' => 'An invisible man is trapped in the terror of his own creation',
            'author' => 'H. G. Wells'
        ]);
        $this->seeStatusCode(201);
        $this->assertTrue($this->response->headers->has('Location'));
        $locationHeader = $this->response->headers->get('Location');
        $this->assertMatchesRegularExpression('#/books/\d+$#', $locationHeader);
    }

    /** @test **/
    public function update_should_only_change_fillable_fields()
    {
        $book = ModelFactory::new()->create([
            'title' => 'War of the Worlds',
            'description' => 'A science fiction masterpiece about Martians invading London',
            'author' => 'H. G. Wells',
        ]);
        $this->put("/books/{$book->id}", [
            'id' => 5,
            'title' => 'The War of the Worlds',
            'description' => 'The book is way better than the movie.',
            'author' => 'Wells, H. G.'
        ]);
        $this
            ->seeStatusCode(200)
            ->seeJson([
                'id' => 1,
                'title' => 'The War of the Worlds',
                'description' => 'The book is way better than the movie.',
                'author' => 'Wells, H. G.'
            ])
            ->seeInDatabase('books', [
                'title' => 'The War of the Worlds'
            ]);
    }

    /** @test **/
    public function update_should_fail_with_an_invalid_id()
    {
        $this
            ->put('/books/999999999999999')
            ->seeStatusCode(404)
            ->seeJsonEquals([
                'error' => [
                    'message' => 'Book not found'
                ]
            ]);
    }

    /** @test **/
    public function update_should_not_match_an_invalid_route()
    {
        $this->put('/books/this-is-invalid')
            ->seeStatusCode(404);
    }

    /** @test **/
    public function destroy_should_remove_a_valid_book()
    {
        $book = ModelFactory::new()->create();
        $this
            ->delete("/books/{$book->id}")
            ->seeStatusCode(204)
            ->isEmpty();
        $this->notSeeInDatabase('books', ['id' => $book->id]);
    }

    /** @test **/
    public function destroy_should_return_a_404_with_an_invalid_id()
    {
        $this
            ->delete('/books/99999')
            ->seeStatusCode(404)
            ->seeJsonEquals([
                'error' => [
                    'message' => 'Book not found'
                ]
            ]);
    }

    /** @test **/
    public function destroy_should_not_match_an_invalid_route()
    {
        $this->delete('/books/this-is-invalid')
            ->seeStatusCode(404);
    }
}
