
<div class="container">
    <h1>Détails Prestation Réalisée</h1>

    <p><strong>Prestation :</strong> {{ $prestationrealisee->prestation->titre ?? '-' }}</p>
    <p><strong>Date :</strong> {{ $prestationrealisee->daterealisation }}</p>
    <p><strong>Statut :</strong> {{ $prestationrealisee->prestationrealiseestatut->titre ?? '-' }}</p>
    <p><strong>Feedback :</strong> {{ $prestationrealisee->feedback ?? '-' }}</p>

    <h3>Bons utilisés</h3>
    <ul>
        @foreach($prestationrealisee->bonutilises as $bon)
            <li>{{ $bon->montant }} FCFA - {{ $bon->bon->code ?? 'N/A' }}</li>
        @endforeach
    </ul>

    <a href="{{ route('prestationrealisee.index') }}" class="btn btn-secondary">Retour</a>
</div>
