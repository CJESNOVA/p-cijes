<?php

use App\Models\Membre;

it('computes nom_complet accessor correctly', function () {
    $membre = new Membre;
    $membre->nom = 'Dupont';
    $membre->prenom = 'Jean';
    expect($membre->nom_complet)->toBe('Dupont Jean');
});

it('has the correct fillable attributes', function () {
    $membre = new Membre;
    expect($membre->getFillable())->toContain(
        'numero_identifiant',
        'nom',
        'prenom',
        'email',
        'membrestatut_id',
        'vignette',
        'membretype_id',
        'user_id',
        'pays_id',
        'telephone',
        'etat'
    );
});

it('uses the membres table', function () {
    $membre = new Membre;
    expect($membre->getTable())->toBe('membres');
});

it('defines membrestatut belongsTo relationship', function () {
    $relation = (new Membre)->membrestatut();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

it('defines membretype belongsTo relationship', function () {
    $relation = (new Membre)->membretype();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

it('defines user belongsTo relationship', function () {
    $relation = (new Membre)->user();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

it('defines reservations hasMany relationship', function () {
    $relation = (new Membre)->reservations();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

it('defines entreprisemembres hasMany relationship', function () {
    $relation = (new Membre)->entreprisemembres();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

it('defines entreprises belongsToMany relationship', function () {
    $relation = (new Membre)->entreprises();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class);
});

it('defines alertes hasMany relationship', function () {
    $relation = (new Membre)->alertes();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

it('defines recompenses hasMany relationship', function () {
    $relation = (new Membre)->recompenses();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});
