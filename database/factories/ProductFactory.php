<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Product;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(Product::class, function (Faker $faker) {
    $name = $faker->name;
    return [
        'title' => $name,
        'slug' => Str::slug($name),
        'price' => $faker->randomNumber(5)
    ];
});
