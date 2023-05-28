<?php

declare(strict_types=1);

$password = '$2y$13$5F1M5JX9Bm68v6fIgxw6CuW80U1zS2a89S9q0QCeMIS0bdiVxKXEm'; // admin

/** @phpstan-ignore-next-line */
return [
    'activeConfirmedUserWithout2fa' => [
        'id' => 1,
        'email' => 'activeConfirmedUserWithout2fa@test.local',
        'password' => $password,
        'is_active' => true,
        'is_confirmed' => true,
        'is_two_fa_active' => false,
    ],
    'activeConfirmedUserWith2fa' => [
        'id' => 2,
        'email' => 'activeConfirmedUserWith2fa@test.local',
        'password' => $password,
        'is_active' => true,
        'is_confirmed' => true,
        'is_two_fa_active' => true,
    ],
    'notActiveUser' => [
        'id' => 3,
        'email' => 'notActiveUser@test.local',
        'password' => $password,
        'is_active' => false,
        'is_confirmed' => true,
        'is_two_fa_active' => true,
    ],
    'notConfirmedUser' => [
        'id' => 4,
        'email' => 'notConfirmedUser@test.local',
        'password' => $password,
        'is_active' => true,
        'is_confirmed' => false,
        'is_two_fa_active' => true,
    ],
    'deletedUser' => [
        'id' => 5,
        'email' => 'deletedUser@test.local',
        'password' => $password,
        'is_active' => true,
        'is_confirmed' => true,
        'is_two_fa_active' => true,
        'deleted_at' => '2022-12-12 00:00:00',
    ],
];