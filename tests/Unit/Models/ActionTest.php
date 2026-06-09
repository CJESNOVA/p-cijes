<?php

use App\Models\Action;

it('has the correct fillable attributes', function () {
    $action = new Action;
    expect($action->getFillable())->toContain(
        'titre',
        'code',
        'point',
        'limite',
        'seuil',
        'ressourcetype_id',
        'spotlight',
        'etat'
    );
});

it('uses the actions table', function () {
    expect((new Action)->getTable())->toBe('actions');
});

it('defines ressourcetype belongsTo relationship', function () {
    $relation = (new Action)->ressourcetype();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});
