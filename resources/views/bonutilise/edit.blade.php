
<div class="container">
    <h1>Modifier Bon Utilisé</h1>

    <form action="{{ route('bonutilise.update', $bonutilise) }}" method="POST">
        @csrf @method('PUT')

        <div class="form-group mb-3">
            <label>Bon</label>
            <select name="bon_id" class="form-control">
                @foreach($bons as $b)
                    <option value="{{ $b->id }}" @if($bonutilise->bon_id == $b->id) selected @endif>{{ $b->code }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label>Prestation réalisée</label>
            <select name="prestationrealisee_id" class="form-control">
                @foreach($prestationrealisees as $p)
                    <option value="{{ $p->id }}" @if($bonutilise->prestationrealisee_id == $p->id) selected @endif>{{ $p->prestation->titre ?? '-' }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label>Montant utilisé</label>
            <input type="number" name="montant" class="form-control" value="{{ $bonutilise->montant }}">
        </div>

        <button class="btn btn-success">Mettre à jour</button>
    </form>
</div>
