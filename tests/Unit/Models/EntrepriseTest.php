<?php

use App\Models\Entreprise;

it('computes numero_identifiant accessor', function () {
    $entreprise = new Entreprise;
    $entreprise->id = 42;
    $entreprise->created_at = '2024-03-15 10:00:00';

    $result = $entreprise->numero_identifiant;

    expect($result)->toBe('CJESTG002403'.'00042');
});

it('pads the id to 5 digits in numero_identifiant', function () {
    $entreprise = new Entreprise;
    $entreprise->id = 1;
    $entreprise->created_at = '2025-01-01 00:00:00';

    $result = $entreprise->numero_identifiant;

    expect($result)->toContain('00001');
});

it('has the correct fillable attributes', function () {
    $entreprise = new Entreprise;
    expect($entreprise->getFillable())->toContain(
        'nom',
        'email',
        'telephone',
        'adresse',
        'description',
        'secteur_id',
        'entreprisetype_id',
        'entrepriseprofil_id',
        'est_membre_cijes',
        'annee_creation',
        'pays_id',
        'etat'
    );
});

it('uses the entreprises table', function () {
    expect((new Entreprise)->getTable())->toBe('entreprises');
});

it('defines secteur belongsTo relationship', function () {
    $relation = (new Entreprise)->secteur();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

it('defines entreprisetype belongsTo relationship', function () {
    $relation = (new Entreprise)->entreprisetype();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

it('defines entrepriseprofil belongsTo relationship', function () {
    $relation = (new Entreprise)->entrepriseprofil();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

it('defines cotisations hasMany relationship', function () {
    $relation = (new Entreprise)->cotisations();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

it('defines abonnements hasMany relationship', function () {
    $relation = (new Entreprise)->abonnements();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

it('defines membres hasMany relationship', function () {
    $relation = (new Entreprise)->membres();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

it('defines entreprisesmembres hasMany relationship', function () {
    $relation = (new Entreprise)->entreprisesmembres();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

it('defines diagnostics hasMany relationship', function () {
    $relation = (new Entreprise)->diagnostics();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});
