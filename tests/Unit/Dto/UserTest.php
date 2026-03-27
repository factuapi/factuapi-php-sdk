<?php

use Factuapi\PhpSdk\Dto\User;

it('maps name and email from response', function () {
    $user = User::fromResponse(['name' => 'John Doe', 'email' => 'john@example.com']);

    expect($user->name)->toBe('John Doe')
        ->and($user->email)->toBe('john@example.com');
});
