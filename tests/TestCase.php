<?php

abstract class TestCase extends Illuminate\Foundation\Testing\TestCase
{
    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    /**
     * Return request headers needed to interact with the API.
     *
     * @return array of headers.
     */
    protected function headers($user = null)
    {
        $headers = ['Accept' => 'application/json'];

        if (!is_null($user)) {
            $token = JWTAuth::fromUser($user);
            JWTAuth::setToken($token);
            $headers['Authorization'] = 'Bearer '.$token;
        }

        return $headers;
    }

    /**
     * Return request headers for basic user
     *
     * @return array of headers.
     */
    protected function headersUser()
    {
        $user = factory(App\User::class)->create();
        return $this->headers($user);
    }

    /**
     * Return request headers for manager
     *
     * @return array of headers.
     */
    protected function headersManager()
    {
        $user = factory(App\User::class, 'manager')->create();
        return $this->headers($user);
    }

    /**
     * Return request headers for manager
     *
     * @return array
     */
    protected function headersAdmin()
    {
        $user = factory(App\User::class, 'admin')->create();
        return $this->headers($user);
    }
}
