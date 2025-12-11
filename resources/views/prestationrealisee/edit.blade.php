
<div class="container">
    <h1>Modifier Prestation Réalisée</h1>

    <form action="{{ route('prestationrealisee.update', $prestationrealisee) }}" method="POST">
        @csrf @method('PUT')

        <div class="form-group mb-3">
            <label>Prestation</label>
            <select name="prestation_id" class="form-control">
                @foreach($prestations as $p)
                    <option value="{{ $p->id }}" @if($prestationrealisee->prestation_id == $p->id) selected @endif>{{ $p->titre }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label>Date de réalisation</label>
            <input type="date" name="daterealisation" class="form-control" value="{{ $prestationrealisee->daterealisation }}">
        </div>

        <div class="form-group mb-3">
            <label>Statut</label>
            <select name="prestationrealiseestatut_id" class="form-control">
                @foreach($statuts as $s)
                    <option value="{{ $s->id }}" @if($prestationrealisee->prestationrealiseestatut_id == $s->id) selected @endif>{{ $s->titre }}</option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-success">Mettre à jour</button>
    </form>
</div>
