
<div class="container">
    <h1>Bons Utilisés</h1>

    <a href="{{ route('bonutilise.create') }}" class="btn btn-primary mb-3">Nouveau bon utilisé</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Montant</th>
                <th>Bon</th>
                <th>Prestation réalisée</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($bonutilises as $bon)
                <tr>
                    <td>{{ $bon->id }}</td>
                    <td>{{ $bon->montant }} FCFA</td>
                    <td>{{ $bon->bon->code ?? '-' }}</td>
                    <td>{{ $bon->prestationrealisee->prestation->titre ?? '-' }}</td>
                    <td>
                        <a href="{{ route('bonutilise.show', $bon) }}" class="btn btn-info btn-sm">Voir</a>
                        <a href="{{ route('bonutilise.edit', $bon) }}" class="btn btn-warning btn-sm">Modifier</a>
                        <form action="{{ route('bonutilise.destroy', $bon) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Aucun bon utilisé</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
