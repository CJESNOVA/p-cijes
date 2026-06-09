<?php

use App\Models\Conversion;

it('has the correct fillable attributes', function () {
    $conversion = new Conversion;
    expect($conversion->getFillable())->toContain(
        'taux',
        'ressourcetransaction_source_id',
        'ressourcetransaction_cible_id',
        'membre_id',
        'entreprise_id',
        'spotlight',
        'etat'
    );
});

it('uses the conversions table', function () {
    expect((new Conversion)->getTable())->toBe('conversions');
});

it('defines ressourcetransactionsource belongsTo relationship', function () {
    $relation = (new Conversion)->ressourcetransactionsource();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

it('defines ressourcetransactioncible belongsTo relationship', function () {
    $relation = (new Conversion)->ressourcetransactioncible();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

it('defines membre belongsTo relationship', function () {
    $relation = (new Conversion)->membre();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

it('defines entreprise belongsTo relationship', function () {
    $relation = (new Conversion)->entreprise();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});
