<?php

use App\Auth\SupabaseUser;

it('returns email as the auth identifier name', function () {
    $user = new SupabaseUser(['email' => 'test@example.com', 'id' => 'uuid-123']);
    expect($user->getAuthIdentifierName())->toBe('email');
});

it('returns email as the auth identifier when email is present', function () {
    $user = new SupabaseUser(['email' => 'test@example.com', 'id' => 'uuid-123']);
    expect($user->getAuthIdentifier())->toBe('test@example.com');
});

it('falls back to id when email is missing', function () {
    $user = new SupabaseUser(['id' => 'uuid-123']);
    expect($user->getAuthIdentifier())->toBe('uuid-123');
});

it('returns null for auth password', function () {
    $user = new SupabaseUser(['email' => 'test@example.com', 'id' => 'uuid-123']);
    expect($user->getAuthPassword())->toBeNull();
});

it('returns null for auth password name', function () {
    $user = new SupabaseUser(['email' => 'test@example.com', 'id' => 'uuid-123']);
    expect($user->getAuthPasswordName())->toBeNull();
});

it('returns user id as remember token', function () {
    $user = new SupabaseUser(['email' => 'test@example.com', 'id' => 'uuid-123']);
    expect($user->getRememberToken())->toBe('uuid-123');
});

it('returns remember_token as the remember token name', function () {
    $user = new SupabaseUser(['email' => 'test@example.com', 'id' => 'uuid-123']);
    expect($user->getRememberTokenName())->toBe('remember_token');
});

it('can set remember token without error', function () {
    $user = new SupabaseUser(['email' => 'test@example.com', 'id' => 'uuid-123']);
    $user->setRememberToken('some-value');
    expect(true)->toBeTrue(); // no exception thrown
});

it('returns the original user array via getUser', function () {
    $data = ['email' => 'test@example.com', 'id' => 'uuid-123', 'name' => 'Test User'];
    $user = new SupabaseUser($data);
    expect($user->getUser())->toBe($data);
});

it('implements Authenticatable interface', function () {
    $user = new SupabaseUser(['email' => 'test@example.com', 'id' => 'uuid-123']);
    expect($user)->toBeInstanceOf(\Illuminate\Contracts\Auth\Authenticatable::class);
});
