<?php

use App\Models\User;

it('has the correct fillable attributes', function () {
    $user = new User;
    expect($user->getFillable())->toContain(
        'name',
        'email',
        'password',
        'supabase_user_id'
    );
});

it('hides password and remember_token', function () {
    $user = new User;
    expect($user->getHidden())->toContain('password', 'remember_token');
});

it('casts email_verified_at to datetime', function () {
    $user = new User;
    $casts = $user->getCasts();
    expect($casts['email_verified_at'])->toBe('datetime');
});

it('casts password to hashed', function () {
    $user = new User;
    $casts = $user->getCasts();
    expect($casts['password'])->toBe('hashed');
});

it('defines membre hasOne relationship', function () {
    $relation = (new User)->membre();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class);
});
