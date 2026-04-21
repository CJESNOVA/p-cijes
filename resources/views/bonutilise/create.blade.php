
<div class="container">
    <h1>Nouveau Bon Utilisé</h1>

    <form action="{{ route('bonutilise.store') }}" method="POST">
        @csrf

        <div class="form-group mb-3">
            <label>Bon</label>
            <select name="bon_id" class="form-control">
                @foreach($bons as $b)
                    <option value="{{ $b->id }}">{{ $b->code }} ({{ $b->montant }} FCFA)</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label>Prestation réalisée</label>
            <select name="prestationrealisee_id" class="form-control">
                @foreach($prestationrealisees as $p)
                    <option value="{{ $p->id }}">{{ $p->prestation->titre ?? '-' }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label>Montant utilisé</label>
            <input type="number" name="montant" class="form-control">
        </div>

        <button class="btn btn-success">Enregistrer</button>
    </form>
</div>
