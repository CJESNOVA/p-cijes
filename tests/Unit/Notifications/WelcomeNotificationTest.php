<?php

use App\Notifications\WelcomeNotification;

it('sends via mail channel only', function () {
    $notification = new WelcomeNotification(userName: 'Jean Dupont');
    $channels = $notification->via(new stdClass);
    expect($channels)->toBe(['mail']);
});

it('stores the userName property', function () {
    $notification = new WelcomeNotification(userName: 'Marie');
    expect($notification->userName)->toBe('Marie');
});
