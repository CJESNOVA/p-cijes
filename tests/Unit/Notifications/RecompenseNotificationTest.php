<?php

use App\Notifications\RecompenseNotification;

it('sends via mail and database channels', function () {
    $notification = new RecompenseNotification(
        actionTitre: 'Test Action',
        points: 100,
        lien: 'https://example.com',
        recompense: ['id' => 1],
        stats: ['total' => 500],
        nextRewards: []
    );

    $channels = $notification->via(new stdClass);
    expect($channels)->toContain('mail');
    expect($channels)->toContain('database');
});

it('converts to array with the correct structure', function () {
    $notification = new RecompenseNotification(
        actionTitre: 'Inscription',
        points: 50,
        lien: 'https://example.com/reward',
        recompense: ['id' => 2, 'valeur' => 50],
        stats: ['total_points' => 200],
        nextRewards: [['name' => 'Bonus', 'points' => 100]]
    );

    $array = $notification->toArray(new stdClass);

    expect($array['titre'])->toBe('Inscription');
    expect($array['points'])->toBe(50);
    expect($array['lien'])->toBe('https://example.com/reward');
    expect($array['recompense'])->toBe(['id' => 2, 'valeur' => 50]);
    expect($array['stats'])->toBe(['total_points' => 200]);
    expect($array['nextRewards'])->toHaveCount(1);
});

it('stores constructor properties correctly', function () {
    $notification = new RecompenseNotification(
        actionTitre: 'Quiz',
        points: 25,
        lien: '/quiz/result'
    );

    expect($notification->actionTitre)->toBe('Quiz');
    expect($notification->points)->toBe(25);
    expect($notification->lien)->toBe('/quiz/result');
    expect($notification->recompense)->toBe([]);
    expect($notification->stats)->toBe([]);
    expect($notification->nextRewards)->toBe([]);
});
