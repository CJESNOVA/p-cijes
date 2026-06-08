@extends('admin.layouts.app')

@section('title', 'Table: ' . $tableName)

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2>📊 Table: {{ $tableName }}</h2>
            <p style="color: #7f8c8d; margin-top: 0.5rem;">
                {{ $data->total() }} enregistrements au total
            </p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.crud.create', $tableName) }}" class="btn">
                ➕ Ajouter un enregistrement
            </a>
            <a href="{{ route('admin.crud.index') }}" class="btn btn-secondary">
                ← Retour aux tables
            </a>
        </div>
    </div>

    <!-- Search and Filters -->
    <div style="display: flex; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 300px; position: relative;">
            <svg style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; color: #7f8c8d;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
            </svg>
            <form method="GET" style="display: flex;">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="Rechercher..." 
                       style="flex: 1; padding: 0.75rem 0.75rem 0.75rem 2.5rem; border: 1px solid #ddd; border-radius: 4px;">
                <button type="submit" class="btn" style="margin-left: 0.5rem;">🔍</button>
                @if(request('search'))
                    <a href="{{ route('admin.crud.table', $tableName) }}" class="btn btn-secondary" style="margin-left: 0.5rem;">✖</a>
                @endif
            </form>
        </div>
        
        <form method="GET" style="display: flex; gap: 0.5rem;">
            <input type="hidden" name="search" value="{{ request('search') }}">
            <select name="per_page" onchange="this.form.submit()" style="padding: 0.75rem; border: 1px solid #ddd; border-radius: 4px;">
                <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 par page</option>
                <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 par page</option>
                <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 par page</option>
                <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 par page</option>
            </select>
        </form>
    </div>

    <!-- Data Table -->
    <div style="overflow-x: auto; background: white; border: 1px solid #e1e8ed; border-radius: 8px;">
        <table style="width: 100%; border-collapse: collapse;">
            <thead style="background: #f8f9fa;">
                <tr>
                    <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e1e8ed; font-weight: 600;">ID</th>
                    @foreach($columns as $column)
                        @if($column != 'id' && $column != 'password' && $column != 'remember_token' && !str_contains($column, 'description') && !str_contains($column, 'created_at') && !str_contains($column, 'updated_at'))
                            <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e1e8ed; font-weight: 600;">
                                {{ ucfirst(str_replace('_', ' ', $column)) }}
                            </th>
                        @endif
                    @endforeach
                    <th style="padding: 1rem; text-align: left; border-bottom: 1px solid #e1e8ed; font-weight: 600;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($data as $row)
                    <tr style="border-bottom: 1px solid #f1f3f4; transition: background-color 0.2s;" 
                        onmouseover="this.style.backgroundColor='#f8f9fa'" 
                        onmouseout="this.style.backgroundColor='transparent'">
                        <td style="padding: 1rem;">
                            <span style="background: #3498db; color: white; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem;">
                                #{{ $row->id }}
                            </span>
                        </td>
                        @foreach($columns as $column)
                            @if($column != 'id' && $column != 'password' && $column != 'remember_token' && !str_contains($column, 'description') && !str_contains($column, 'created_at') && !str_contains($column, 'updated_at'))
                                <td style="padding: 1rem; max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                    @if(isset($columns[$column]) && str_contains($columns[$column]['type'], 'tinyint(1)'))
                                        <label style="display: flex; align-items: center; cursor: pointer;">
                                            <input type="checkbox" 
                                                   {{ $row->$column == 1 ? 'checked' : '' }}
                                                   onchange="toggleBoolean('{{$tableName}}', '{{$row->id}}', '{{$column}}', this)"
                                                   style="margin-right: 0.5rem;">
                                            <span>{{ $row->$column == 1 ? '✅' : '❌' }}</span>
                                        </label>
                                    @elseif(isset($foreignKeyOptions[$column]) && $foreignKeyOptions[$column] && $row->$column)
                                        <!-- Afficher le nom de la clé étrangère -->
                                        @php
                                            $foreignValue = $foreignKeyOptions[$column]['options']->where('id', $row->$column)->first();
                                        @endphp
                                        @if($foreignValue)
                                            <span style="background: #e8f4fd; color: #1976d2; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem;">
                                                {{ $foreignValue->display }}
                                            </span>
                                        @else
                                            <span style="color: #e74c3c;">#{{ $row->$column }} (non trouvé)</span>
                                        @endif
                                    @else
                                        {{ $row->$column ?? '-' }}
                                    @endif
                                </td>
                            @endif
                        @endforeach
                        <td style="padding: 1rem;">
                            <div style="display: flex; gap: 0.5rem;">
                                <a href="{{ route('admin.crud.show', [$tableName, $row->id]) }}" 
                                   class="btn btn-secondary" 
                                   style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" 
                                   title="Voir les détails">
                                    👁️
                                </a>
                                <a href="{{ route('admin.crud.edit', [$tableName, $row->id]) }}" 
                                   class="btn" 
                                   style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" 
                                   title="Modifier">
                                    ✏️
                                </a>
                                <form method="POST" action="{{ route('admin.crud.destroy', [$tableName, $row->id]) }}" 
                                      style="display: inline;" 
                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet enregistrement ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="btn btn-danger" 
                                            style="padding: 0.25rem 0.5rem; font-size: 0.8rem;" 
                                            title="Supprimer">
                                        🗑️
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($data->isEmpty())
            <tr>
                <td colspan="{{ count($columns) + 2 }}" style="padding: 3rem; text-align: center; color: #7f8c8d;">
                    <svg style="width: 64px; height: 64px; margin-bottom: 1rem;" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="11" cy="11" r="8"/>
                        <path d="m21 21-4.35-4.35"/>
                        <line x1="8" y1="11" x2="14" y2="11"/>
                    </svg>
                    <h3>Aucun enregistrement trouvé</h3>
                    <p>
                        @if(request('search'))
                            Aucun résultat pour "{{ request('search') }}"
                        @else
                            Cette table est vide
                        @endif
                    </p>
                    @if(!request('search'))
                        <a href="{{ route('admin.crud.create', $tableName) }}" class="btn" style="margin-top: 1rem;">
                            ➕ Ajouter le premier enregistrement
                        </a>
                    @endif
                </td>
            </tr>
        @endif
    </div>

    <!-- Pagination -->
    @if($data->hasPages())
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; flex-wrap: wrap; gap: 1rem;">
            <div style="color: #7f8c8d;">
                Affichage de {{ $data->firstItem() }} à {{ $data->lastItem() }} sur {{ $data->total() }} enregistrements
            </div>
            
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                {{-- Previous Button --}}
                @if($data->onFirstPage())
                    <button class="btn btn-secondary" disabled>
                        ← Précédent
                    </button>
                @else
                    <a href="{{ $data->previousPageUrl() }}" class="btn btn-secondary">
                        ← Précédent
                    </a>
                @endif

                {{-- Pagination Links --}}
                {!! $data->links() !!}

                {{-- Next Button --}}
                @if($data->hasMorePages())
                    <a href="{{ $data->nextPageUrl() }}" class="btn btn-secondary">
                        Suivant →
                    </a>
                @else
                    <button class="btn btn-secondary" disabled>
                        Suivant →
                    </button>
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

<script>
function toggleBoolean(tableName, id, column, checkbox) {
    // Désactiver temporairement la case à cocher
    checkbox.disabled = true;
    
    fetch(`/admin/crud/${tableName}/${id}/toggle/${column}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            checkbox.checked = data.new_value == 1;
            // Mettre à jour l'icône
            const span = checkbox.nextElementSibling;
            span.textContent = data.new_value == 1 ? '✅' : '❌';
            
            // Afficher un message de succès
            showToast(data.message, 'success');
        } else {
            // Revenir à l'état original
            checkbox.checked = !checkbox.checked;
            showToast(data.error || 'Erreur lors de la mise à jour', 'error');
        }
    })
    .catch(error => {
        // Revenir à l'état original
        checkbox.checked = !checkbox.checked;
        showToast('Erreur réseau', 'error');
        console.error('Error:', error);
    })
    .finally(() => {
        // Réactiver la case à cocher
        checkbox.disabled = false;
    });
}

function showToast(message, type = 'info') {
    // Créer un toast simple
    const toast = document.createElement('div');
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        background: ${type === 'success' ? '#27ae60' : type === 'error' ? '#e74c3c' : '#3498db'};
        color: white;
        border-radius: 8px;
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        z-index: 9999;
        opacity: 0;
        transform: translateX(100%);
        transition: all 0.3s ease;
    `;
    toast.textContent = message;
    
    document.body.appendChild(toast);
    
    // Animer l'entrée
    setTimeout(() => {
        toast.style.opacity = '1';
        toast.style.transform = 'translateX(0)';
    }, 100);
    
    // Disparaître après 3 secondes
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(100%)';
        setTimeout(() => {
            document.body.removeChild(toast);
        }, 300);
    }, 3000);
}
</script>
