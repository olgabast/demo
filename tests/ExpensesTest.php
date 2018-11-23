<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ExpensesTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     *
     * Test POST /api/expenses
     */
    public function testItCanBeCreated()
    {
        $user = factory(App\User::class)->create();
        $expense = factory(App\Expense::class)->make();

        $this->post('/api/expenses', $expense->toArray(), $this->headers($user))
             ->seeStatusCode(201)
             ->seeInDatabase('expenses', ['description' => $expense->description])
             ->seeJson([
                 'description' => $expense->description,
                 'user_id' => $user->id
             ]);
    }

    /**
     * @test
     *
     * Test GET /api/expenses
     */
    public function testItCanBeListed()
    {
        $user = factory(App\User::class)->create();
        $expense = $user->expenses()->save(factory(App\Expense::class)->make());

        $this->get('/api/expenses', $this->headers($user))
             ->seeStatusCode(200)
             ->seeJson([
                 'description' => $expense->description,
                 'user_id' => $user->id
             ])
             ->seeJsonStructure([
                 'data' => [
                     '*' => [
                         'id', 'user_id', 'datetime',  'description', 'amount', 'comment'
                     ]
                 ]
             ]);
    }

    /**
     * @test
     *
     * Test PUT /api/expenses/{expense}
     */
    public function testItCanBeUpdated()
    {
        $user = factory(App\User::class)->create();
        $expense = $user->expenses()->save(factory(App\Expense::class)->make(['description' => 'old']));
        $expense->description = 'new';

        $this->put('/api/expenses/' . $expense->id, $expense->toArray(), $this->headers($user))
             ->seeStatusCode(200)
             ->seeJson([
                 'amount'      => $expense->amount,
                 'description' => $expense->description,
             ])
             ->seeInDatabase('expenses', ['description' => 'new']);
    }

    /**
     * @test
     *
     * Test DELETE /api/expenses/{expense}
     */
    public function testItCanBeDeleted()
    {
        $user = factory(App\User::class)->create();
        $expense = $user->expenses()->save(factory(App\Expense::class)->make());

        $this->delete('/api/expenses/' . $expense->id, [], $this->headers($user))
             ->seeStatusCode(200)
             ->notSeeInDatabase('expenses', ['description' => $expense->description]);
    }

    /**
     * @test
     *
     * Test requests without authorization
     */
    public function testItCanBeAccessedOnlyByAuthenticatedUser()
    {
        $user = factory(App\User::class)->create();
        $expense = $user->expenses()->save(factory(App\Expense::class)->make());

        $this->get('/api/expenses')
             ->seeStatusCode(401);

        $this->post('/api/expenses', [])
             ->seeStatusCode(401);

        $this->put('/api/expenses/' . $expense->id, [])
             ->seeStatusCode(401);

        $this->delete('/api/expenses/' . $expense->id , [])
             ->seeStatusCode(401);
    }

    /**
     * @test
     *
     * Test GET /api/expenses/all by user
     */
    public function testUserCanNotListAll()
    {
        $this->get('/api/expenses/all', $this->headersUser())
            ->seeStatusCode(403);
    }

    /**
     * @test
     *
     * Test GET /api/expenses/all by manager
     */
    public function testManagerCanNotListAll()
    {
        $this->get('/api/expenses/all', $this->headersManager())
            ->seeStatusCode(403);
    }

    /**
     * @test
     *
     * Test GET /api/expenses/all by admin
     */
    public function testAdminCanListAll()
    {
        $user = factory(App\User::class)->create();
        $expense = $user->expenses()->save(factory(App\Expense::class)->make());

        $this->get('/api/expenses/all', $this->headersAdmin())
            ->seeStatusCode(200)
            ->seeJson([
                'description' => $expense->description,
                'user_id' => $user->id
            ])
            ->seeJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'user_id', 'datetime',  'description', 'amount', 'comment'
                    ]
                ]
            ]);
    }

    /**
     * @test
     *
     * Test PUT /api/expenses/{expense} by user who does not own it
     */
    public function testUserCanNotUpdateAll()
    {
        $user = factory(App\User::class)->create();
        $expense = $user->expenses()->save(factory(App\Expense::class)->make());

        $this->put('/api/expenses/' . $expense->id, [], $this->headersUser())
            ->seeStatusCode(403);
    }

    /**
     * @test
     *
     * Test PUT /api/expenses/{expense} by manager who does not own it
     */
    public function testManagerCanNotUpdateAll()
    {
        $user = factory(App\User::class)->create();
        $expense = $user->expenses()->save(factory(App\Expense::class)->make());

        $this->put('/api/expenses/' . $expense->id, [], $this->headersManager())
            ->seeStatusCode(403);
    }

    /**
     * @test
     *
     * Test PUT /api/expenses/{expense} by admin who does not own it
     */
    public function testAdminCanUpdateNotOwned()
    {
        $user = factory(App\User::class)->create();
        $expense = $user->expenses()->save(factory(App\Expense::class)->make());
        $expense->description = 'new';

        $this->put('/api/expenses/' . $expense->id, $expense->toArray(), $this->headersAdmin())
            ->seeStatusCode(200)
            ->seeJson([
                'description' => $expense->description,
            ])
            ->seeInDatabase('expenses', ['description' => $expense->description]);;
    }

    /**
     * @test
     *
     * Test DELETE /api/expenses/{expense} by user who does not own it
     */
    public function testUserCanNotDeleteNotOwned()
    {
        $user = factory(App\User::class)->create();
        $expense = $user->expenses()->save(factory(App\Expense::class)->make());

        $this->delete('/api/expenses/' . $expense->id, [], $this->headersUser())
            ->seeStatusCode(403);
    }

    /**
     * @test
     *
     * Test DELETE /api/expenses/{expense} by manager who does not own it
     */
    public function testManagerCanNotDeleteNotOwned()
    {
        $user = factory(App\User::class)->create();
        $expense = $user->expenses()->save(factory(App\Expense::class)->make());

        $this->delete('/api/expenses/' . $expense->id, [], $this->headersManager())
            ->seeStatusCode(403);
    }

    /**
     * @test
     *
     * Test DELETE /api/expenses/{expense} by admin who does not own it
     */
    public function testAdminCanDeleteNotOwned()
    {
        $user = factory(App\User::class)->create();
        $expense = $user->expenses()->save(factory(App\Expense::class)->make());

        $this->delete('/api/expenses/' . $expense->id, [], $this->headersAdmin())
            ->seeStatusCode(200)
            ->notSeeInDatabase('expenses', ['id' => $expense->id]);
    }

    /**
     * @test
     *
     * Test POST /api/expenses validation
     */
    public function testItValidatesCreate()
    {
        $this->post('/api/expenses', [], $this->headersAdmin())
            ->seeStatusCode(422)
            ->seeJson(["The amount field is required."])
            ->seeJson(["The datetime field is required."]);
    }

    /**
     * @test
     *
     * Test PUT /api/expenses validation
     */
    public function testItValidatesUpdate()
    {
        $user = factory(App\User::class)->create();
        $expense = $user->expenses()->save(factory(App\Expense::class)->make());

        $this->put('/api/expenses/' . $expense->id, [], $this->headersAdmin())
            ->seeStatusCode(422)
            ->seeJson(["The amount field is required."])
            ->seeJson(["The datetime field is required."]);
    }
}
