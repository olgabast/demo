<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UsersAuthTest extends TestCase
{
    use DatabaseMigrations;

    /**
     * @test
     *
     * Test POST /api/auth/signup
     */
    public function testItCanSignUp()
    {
        $data = [
            'email'    => 'test@user.com',
            'password' => 'secret'
        ];
        $this->post('/api/auth/register', $data)
             ->seeStatusCode(201)
             ->seeJsonStructure(['token'])
             ->seeInDatabase('users', ['email' => 'test@user.com']);
    }

    /**
     * @test
     *
     * Test POST /api/auth/login
     */
    public function testItCanLogin()
    {
        $user = factory(App\User::class)->create(['password' => 'secret']);

        $this->post('/api/auth/login', ['email' => $user->email, 'password' => 'secret'])
             ->seeJsonStructure(['token']);
    }

    /**
     * @test
     *
     * Test POST /api/auth/login with bad data
     */
    public function testItCanNotLoginBadCredentials()
    {
        $this->post('/api/auth/login', ['email' => 'ghost@user.com'])
             ->seeStatusCode(422);
        $this->post('/api/auth/login', ['password' => 'secret'])
             ->seeStatusCode(422);
        $this->post('/api/auth/login', ['email' => 'ghost@user.com', 'password' => 'secret'])
             ->seeStatusCode(401);
    }
}
