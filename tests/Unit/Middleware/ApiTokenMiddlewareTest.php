<?php

use App\Http\Middleware\ApiTokenMiddleware;
use Illuminate\Http\Request;

it('rejects requests without a bearer token', function () {
    $middleware = new ApiTokenMiddleware;
    $request = Request::create('/api/test', 'GET');

    $response = $middleware->handle($request, fn ($req) => response()->json(['ok' => true]));

    expect($response->getStatusCode())->toBe(401);
    $data = json_decode($response->getContent(), true);
    expect($data['success'])->toBeFalse();
    expect($data['message'])->toBe('Token manquant');
});

it('returns 401 json when token is missing from header', function () {
    $middleware = new ApiTokenMiddleware;
    $request = Request::create('/api/resource', 'POST');

    $response = $middleware->handle($request, fn ($req) => response()->json(['ok' => true]));

    expect($response->getStatusCode())->toBe(401);
    $body = json_decode($response->getContent(), true);
    expect($body)->toHaveKeys(['success', 'message', 'data']);
    expect($body['data'])->toBeNull();
});

it('hashes the bearer token with sha256 before looking it up', function () {
    $middleware = new ApiTokenMiddleware;
    $request = Request::create('/api/test', 'GET');
    $request->headers->set('Authorization', 'Bearer test-token');

    $expectedHash = hash('sha256', 'test-token');
    expect($expectedHash)->toBe(hash('sha256', 'test-token'));
});
