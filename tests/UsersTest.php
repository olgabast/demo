<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PermissionsTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     *
     * Test POST /api/users by user
     */
    public function testUserCanNotCreateUsers()
    {
        $this->post('/api/users', [], $this->headersUser())
            ->seeStatusCode(403);
    }

    /**
     * @test
     *
     * Test GET /api/users by user
     */
    public function testUserCanNotListUsers()
    {
        $this->get('/api/users', $this->headersUser())
            ->seeStatusCode(403);
    }

    /**
     * @test
     *
     * Test UPDATE /api/users by user
     */
    public function testUserCanNotUpdateUsers()
    {
        $user = factory(App\User::class)->create();

        $this->put('/api/users/' . $user->id, [], $this->headersUser())
            ->seeStatusCode(403);
    }

    /**
     * @test
     *
     * Test DELETE /api/users by user
     */
    public function testUserCanNotDeleteUsers()
    {
        $user = factory(App\User::class)->create();

        $this->delete('/api/users/' . $user->id, [], $this->headersUser())
            ->seeStatusCode(403);
    }

    /**
     * @test
     *
     * Test POST /api/users by manager
     */
    public function testManagerCanCreateUsers()
    {
        $user = factory(App\User::class)->make();
        $user_data = array_merge($user->toArray(), ['password' => 'secret']);

        $this->post('/api/users', $user_data, $this->headersManager())
            ->seeStatusCode(201)
            ->seeInDatabase('users', ['email' => $user->email])
            ->seeJson([
                'email' => $user->email
            ]);
    }

    /**
     * @test
     *
     * Test GET /api/users by manager
     */
    public function testManagerCanListUsers()
    {
        $user = factory(App\User::class)->create();

        $this->get('/api/users', $this->headersManager())
            ->seeStatusCode(200)
            ->seeJson([
                'email' => $user->email
            ])
            ->seeJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'email', 'role'
                    ]
                ]
            ]);
    }

    /**
     * @test
     *
     * Test UPDATE /api/users by manager
     */
    public function testManagerCanUpdateUsers()
    {
        $user = factory(App\User::class)->create();
        $new_user_data = array_merge($user->toArray(), ['email' => 'new@email.com', 'password' => 'secret']);

        $this->put('/api/users/' . $user->id, $new_user_data, $this->headersManager())
            ->seeStatusCode(200)
            ->seeJson([
                'email' => $new_user_data['email']
            ])
            ->seeInDatabase('users', ['email' => $new_user_data['email']]);
    }

    /**
     * @test
     *
     * Test DELETE /api/users by manager
     */
    public function testManagerCanDeleteUsers()
    {
        $user = factory(App\User::class)->create();

        $this->delete('/api/users/' . $user->id, [], $this->headersManager())
            ->seeStatusCode(200)
            ->notSeeInDatabase('users', ['email' => $user->email]);
    }


    /**
     * @test
     *
     * Test POST /api/users by manager
     */
    public function testAdminCanCreateUsers()
    {
        $user = factory(App\User::class)->make();
        $user_data = array_merge($user->toArray(), ['password' => 'secret']);

        $this->post('/api/users', $user_data, $this->headersAdmin())
            ->seeStatusCode(201)
            ->seeInDatabase('users', ['email' => $user->email])
            ->seeJson([
                'email' => $user->email
            ]);
    }

    /**
     * @test
     *
     * Test GET /api/users by manager
     */
    public function testAdminCanListUsers()
    {
        $user = factory(App\User::class)->create();

        $this->get('/api/users', $this->headersAdmin())
            ->seeStatusCode(200)
            ->seeJson([
                'email' => $user->email
            ])
            ->seeJsonStructure([
                'data' => [
                    '*' => [
                        'id', 'email', 'role'
                    ]
                ]
            ]);
    }

    /**
     * @test
     *
     * Test UPDATE /api/users by manager
     */
    public function testAdminCanUpdateUsers()
    {
        $user = factory(App\User::class)->create();
        $new_user_data = array_merge($user->toArray(), ['email' => 'new@email.com', 'password' => 'secret']);

        $this->put('/api/users/' . $user->id, $new_user_data, $this->headersAdmin())
            ->seeStatusCode(200)
            ->seeJson([
                'email' => $new_user_data['email']
            ])
            ->seeInDatabase('users', ['email' => $new_user_data['email']]);
    }

    /**
     * @test
     *
     * Test DELETE /api/users by manager
     */
    public function testAdminCanDeleteUsers()
    {
        $user = factory(App\User::class)->create();

        $this->delete('/api/users/' . $user->id, [], $this->headersAdmin())
            ->seeStatusCode(200)
            ->notSeeInDatabase('users', ['email' => $user->email]);
    }

    /**
     * @test
     *
     * Test POST /api/users validation
     */
    public function testItValidatesCreate()
    {
        $this->post('/api/users', [], $this->headersAdmin())
            ->seeStatusCode(422)
            ->seeJson(["The email field is required."])
            ->seeJson(["The password field is required."])
            ->seeJson(["The role field is required."]);
    }

    /**
     * @test
     *
     * Test PUT /api/users validation
     */
    public function testItValidatesUpdate()
    {
        $user = factory(App\User::class)->create();

        $this->put('/api/users/' . $user->id, [], $this->headersAdmin())
            ->seeStatusCode(422)
            ->seeJson(["The email field is required."])
            ->seeJson(["The role field is required."]);
    }

    /**
     * @test
     *
     * Test requests without authorization
     */
    public function testItCanBeAccessedOnlyByAuthenticatedUser()
    {
        $user = factory(App\User::class)->create();

        $this->get('/api/users')
            ->seeStatusCode(401);

        $this->post('/api/users', [])
            ->seeStatusCode(401);

        $this->put('/api/users/' . $user->id, [])
            ->seeStatusCode(401);

        $this->delete('/api/users/' . $user->id , [])
            ->seeStatusCode(401);
    }
}