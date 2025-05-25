<?php

use Illuminate\Foundation\Testing\WithFaker;

uses(WithFaker::class);

test('faker name generation works', function () {
    $name = $this->faker->name();
    expect($name)->toBeString()->not->toBeEmpty();
});
