
<div class="container">
    <h1>Détails Bon Utilisé</h1>

    <p><strong>Bon :</strong> {{ $bonutilise->bon->code ?? '-' }}</p>
    <p><strong>Montant utilisé :</strong> {{ $bonutilise->montant }} FCFA</p>
    <p><strong>Prestation réalisée :</strong> {{ $bonutilise->prestationrealisee->prestation->titre ?? '-' }}</p>

    <a href="{{ route('bonutilise.index') }}" class="btn btn-secondary">Retour</a>
</div>
