<?php

use App\Models\Ressourcecompte;

it('has the correct fillable attributes', function () {
    $compte = new Ressourcecompte;
    expect($compte->getFillable())->toContain(
        'solde',
        'membre_id',
        'ressourcetype_id',
        'entreprise_id',
        'user_id',
        'spotlight',
        'etat'
    );
});

it('uses the ressourcecomptes table', function () {
    expect((new Ressourcecompte)->getTable())->toBe('ressourcecomptes');
});

it('appends nom_complet', function () {
    $compte = new Ressourcecompte;
    expect($compte->getAppends())->toContain('nom_complet');
});

it('defines membre belongsTo relationship', function () {
    $relation = (new Ressourcecompte)->membre();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

it('defines user belongsTo relationship', function () {
    $relation = (new Ressourcecompte)->user();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

it('defines ressourcetype belongsTo relationship', function () {
    $relation = (new Ressourcecompte)->ressourcetype();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

it('defines entreprise belongsTo relationship', function () {
    $relation = (new Ressourcecompte)->entreprise();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

it('defines ressourcetransactions hasMany relationship', function () {
    $relation = (new Ressourcecompte)->ressourcetransactions();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

it('computes nom_complet with solde formatted', function () {
    $compte = new Ressourcecompte;
    $compte->solde = 1234.5;

    $result = $compte->nom_complet;

    expect($result)->toContain('1,234.50');
});
