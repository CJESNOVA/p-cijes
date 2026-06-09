<?php

use App\Models\Diagnostic;

it('has the correct fillable attributes', function () {
    $diagnostic = new Diagnostic;
    expect($diagnostic->getFillable())->toContain(
        'scoreglobal',
        'commentaire',
        'diagnostictype_id',
        'diagnosticstatut_id',
        'membre_id',
        'entreprise_id',
        'entrepriseprofil_id',
        'spotlight',
        'etat'
    );
});

it('uses the diagnostics table', function () {
    expect((new Diagnostic)->getTable())->toBe('diagnostics');
});

it('casts diagnostictype_id to integer', function () {
    $diagnostic = new Diagnostic;
    $casts = $diagnostic->getCasts();
    expect($casts['diagnostictype_id'])->toBe('integer');
});

it('casts spotlight to boolean', function () {
    $diagnostic = new Diagnostic;
    $casts = $diagnostic->getCasts();
    expect($casts['spotlight'])->toBe('boolean');
});

it('casts etat to boolean', function () {
    $diagnostic = new Diagnostic;
    $casts = $diagnostic->getCasts();
    expect($casts['etat'])->toBe('boolean');
});

it('appends nom_complet attribute', function () {
    $diagnostic = new Diagnostic;
    $appends = $diagnostic->getAppends();
    expect($appends)->toContain('nom_complet');
});

it('defines diagnostictype belongsTo relationship', function () {
    $relation = (new Diagnostic)->diagnostictype();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

it('defines diagnosticstatut belongsTo relationship', function () {
    $relation = (new Diagnostic)->diagnosticstatut();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

it('defines membre belongsTo relationship', function () {
    $relation = (new Diagnostic)->membre();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

it('defines entreprise belongsTo relationship', function () {
    $relation = (new Diagnostic)->entreprise();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

it('defines entrepriseprofil belongsTo relationship', function () {
    $relation = (new Diagnostic)->entrepriseprofil();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

it('defines diagnosticmodulescores hasMany relationship', function () {
    $relation = (new Diagnostic)->diagnosticmodulescores();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

it('defines diagnosticresultats hasMany relationship', function () {
    $relation = (new Diagnostic)->diagnosticresultats();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

it('defines accompagnements hasMany relationship', function () {
    $relation = (new Diagnostic)->accompagnements();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

it('defines accompagnement hasOne relationship', function () {
    $relation = (new Diagnostic)->accompagnement();
    expect($relation)->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasOne::class);
});

it('computes nom_complet with score when no relations loaded', function () {
    $diagnostic = new Diagnostic;
    $diagnostic->id = 5;
    $diagnostic->scoreglobal = 85;

    $result = $diagnostic->nom_complet;

    expect($result)->toContain('Diagnostic #5');
    expect($result)->toContain('Score: 85');
});
