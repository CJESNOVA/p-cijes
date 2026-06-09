<?php

use App\Models\Recompense;

it('has the correct fillable attributes', function () {
    $recompense = new Recompense;
    expect($recompense->getFillable())->toContain(
        'valeur',
        'commentaire',
        'action_id',
        'ressourcetype_id',
        'dateattribution',
        'membre_id',
        'entreprise_id',
        'source_id',
        'spotlight',
        'etat'
    );
});

it('uses the recompenses table', function () {
    expect((new Recompense)->getTable())->toBe('recompenses');
});

it('defines action belongsTo relationship', function () {
    $relation = (new Recompense)->action();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

it('defines ressourcetype belongsTo relationship', function () {
    $relation = (new Recompense)->ressourcetype();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

it('defines membre belongsTo relationship', function () {
    $relation = (new Recompense)->membre();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

it('defines entreprise belongsTo relationship', function () {
    $relation = (new Recompense)->entreprise();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});
