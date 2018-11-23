<?php

namespace App\Providers;

use Dingo\Api\Http\Response;
use Illuminate\Auth\Access\AuthorizationException;

use Illuminate\Support\ServiceProvider;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /* register ide helper service provider*/
        if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        $this->app['api.exception']->register(function (AuthorizationException $exception) {
            throw new AccessDeniedHttpException($exception->getMessage(), $exception);
        });

        $this->app['api.exception']->register(function(\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return Response::create(
                ['message' => 'Resource not found'],
                404);
        });
    }
}
