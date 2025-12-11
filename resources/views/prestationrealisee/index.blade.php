
<div class="container">
    <h1>Prestations Réalisées</h1>
    <a href="{{ route('prestationrealisee.create') }}" class="btn btn-primary mb-3">Nouvelle prestation réalisée</a>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>ID</th>
                <th>Prestation</th>
                <th>Statut</th>
                <th>Date réalisation</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($prestationrealisees as $prestation)
                <tr>
                    <td>{{ $prestation->id }}</td>
                    <td>{{ $prestation->prestation->titre ?? '-' }}</td>
                    <td>{{ $prestation->prestationrealiseestatut->titre ?? '-' }}</td>
                    <td>{{ $prestation->daterealisation ?? '-' }}</td>
                    <td>
                        <a href="{{ route('prestationrealisee.show', $prestation) }}" class="btn btn-info btn-sm">Voir</a>
                        <a href="{{ route('prestationrealisee.edit', $prestation) }}" class="btn btn-warning btn-sm">Modifier</a>
                        <form action="{{ route('prestationrealisee.destroy', $prestation) }}" method="POST" class="d-inline">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Supprimer ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5">Aucune prestation réalisée</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
