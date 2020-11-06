<?php

use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Models\User::class, function (Faker $faker) {
    return [
        'name'              => $faker->firstName,
        'last_name'         => $faker->lastName,
        'email'             => $faker->unique()->safeEmail,
        'password'          => 'secret',
        'remember_token'    => str_random(10),
        'is_confirmed'      => true,
        'chosen_tenant_id'  => 1
    ];
});

$factory->define(App\Models\Customer::class, function (Faker $faker) {
    return [
        'name'          => $faker->company,
        'contact'       => $faker->name,
        'city'          => $faker->city,
        'country_id'    => \App\Models\Country::all()->random()->id,
        'email'         => $faker->unique()->safeEmail,
        'telephone'     => $faker->e164PhoneNumber,
        'street'        => $faker->streetAddress,
        'postcode'      => $faker->postcode,
        'house'         => $faker->randomDigitNotNull,
        'custom_id'     => $faker->unique()->randomDigit,
        'hourly_rate'   => $faker->randomFloat(2, 1, 250),
    ];
});
