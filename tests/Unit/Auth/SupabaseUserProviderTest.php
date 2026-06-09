<?php

use App\Auth\SupabaseUser;
use App\Auth\SupabaseUserProvider;

beforeEach(function () {
    $this->provider = new SupabaseUserProvider;
});

it('returns null from retrieveById when no session user exists', function () {
    session()->forget('supabase_user');
    expect($this->provider->retrieveById('anything'))->toBeNull();
});

it('returns a SupabaseUser from retrieveById when session user exists', function () {
    session(['supabase_user' => ['email' => 'test@example.com', 'id' => 'uuid-1']]);
    $user = $this->provider->retrieveById('uuid-1');
    expect($user)->toBeInstanceOf(SupabaseUser::class);
    expect($user->getAuthIdentifier())->toBe('test@example.com');
});

it('returns null from retrieveByToken', function () {
    expect($this->provider->retrieveByToken('id', 'token'))->toBeNull();
});

it('returns null from retrieveByCredentials', function () {
    expect($this->provider->retrieveByCredentials(['email' => 'a@b.com']))->toBeNull();
});

it('always validates credentials as true', function () {
    $user = new SupabaseUser(['email' => 'test@example.com', 'id' => 'uuid-1']);
    expect($this->provider->validateCredentials($user, ['password' => 'any']))->toBeTrue();
});

it('returns null from rehashPasswordIfRequired', function () {
    $user = new SupabaseUser(['email' => 'test@example.com', 'id' => 'uuid-1']);
    expect($this->provider->rehashPasswordIfRequired($user, []))->toBeNull();
});

it('updateRememberToken does not throw', function () {
    $user = new SupabaseUser(['email' => 'test@example.com', 'id' => 'uuid-1']);
    $this->provider->updateRememberToken($user, 'new-token');
    expect(true)->toBeTrue();
});

it('implements UserProvider interface', function () {
    expect($this->provider)->toBeInstanceOf(\Illuminate\Contracts\Auth\UserProvider::class);
});
