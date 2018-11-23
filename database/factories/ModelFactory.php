<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'email' => $faker->safeEmail,
        'password' => $password ?: $password = 'secret',
        'role' => 'user',
        'remember_token' => str_random(10),
    ];
});

$factory->defineAs(App\User::class, 'manager', function (Faker\Generator $faker) use ($factory) {
    $user = $factory->raw(App\User::class);
    return array_merge($user, ['role' => 'manager']);
});

$factory->defineAs(App\User::class, 'admin', function (Faker\Generator $faker) use ($factory) {
    $user = $factory->raw(App\User::class);
    return array_merge($user, ['role' => 'admin']);
});

$factory->define(App\Expense::class, function (Faker\Generator $faker) {
    return [
        'datetime' => $faker->dateTimeThisYear,
        'description' => $faker->sentence,
        'amount' => $faker->randomFloat(2, 0, 100000),
        'comment' => $faker->sentence
    ];
});
