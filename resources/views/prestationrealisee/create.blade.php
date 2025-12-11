
<div class="container">
    <h1>Nouvelle Prestation Réalisée</h1>

    <form action="{{ route('prestationrealisee.store') }}" method="POST">
        @csrf

        <div class="form-group mb-3">
            <label>Prestation</label>
            <select name="prestation_id" class="form-control">
                @foreach($prestations as $p)
                    <option value="{{ $p->id }}">{{ $p->titre }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group mb-3">
            <label>Date de réalisation</label>
            <input type="date" name="daterealisation" class="form-control">
        </div>

        <div class="form-group mb-3">
            <label>Statut</label>
            <select name="prestationrealiseestatut_id" class="form-control">
                @foreach($statuts as $s)
                    <option value="{{ $s->id }}">{{ $s->titre }}</option>
                @endforeach
            </select>
        </div>

        <button class="btn btn-success">Enregistrer</button>
    </form>
</div>
