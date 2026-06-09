<?php

use App\Models\ApiToken;
use Carbon\Carbon;

it('reports as expired when expires_at is in the past', function () {
    $token = new ApiToken;
    $token->expires_at = Carbon::now()->subHour();
    expect($token->isExpired())->toBeTrue();
});

it('reports as not expired when expires_at is in the future', function () {
    $token = new ApiToken;
    $token->expires_at = Carbon::now()->addHour();
    expect($token->isExpired())->toBeFalse();
});

it('casts expires_at to a Carbon datetime', function () {
    $token = new ApiToken;
    $token->expires_at = '2030-01-01 00:00:00';
    expect($token->expires_at)->toBeInstanceOf(Carbon::class);
});

it('has user_id, token, and expires_at as fillable', function () {
    $token = new ApiToken;
    expect($token->getFillable())->toContain('user_id', 'token', 'expires_at');
});

it('defines a user belongsTo relationship', function () {
    $token = new ApiToken;
    $relation = $token->user();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});
