@extends('admin.layouts.app')

@section('title', 'CRUD Dynamique - Gestion des tables')
@section('page-title', 'Gestion des Tables')
@section('page-subtitle', 'Accédez et modifiez toutes les données de la base de données')

@section('content')
<!-- Search and Filters -->
<div class="card">
    <div class="card-title">🔍 Recherche et Filtres</div>
    <div style="display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap;">
        <div style="flex: 1; min-width: 300px; position: relative;">
            <svg style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); width: 20px; height: 20px; color: var(--text-muted);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"/>
                <path d="m21 21-4.35-4.35"/>
            </svg>
            <input type="text" placeholder="Rechercher une table..." 
                   class="form-input"
                   style="padding-left: 2.5rem;"
                   onkeyup="filterTables(this.value)">
        </div>
        
        <select class="form-select" style="min-width: 200px;" onchange="filterByType(this.value)">
            <option value="">Toutes les tables</option>
            <option value="users">Tables utilisateurs</option>
            <option value="business">Tables métier</option>
            <option value="system">Tables système</option>
        </select>

        <button class="btn btn-secondary" onclick="location.reload()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="23 4 23 10 17 10"/>
                <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
            </svg>
            Actualiser
        </button>
    </div>
</div>

    <!-- Tables Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 1.5rem;">
    @foreach($tableData as $table)
        <div class="card table-card" data-table-name="{{ $table['name'] }}" data-table-type="{{ $table['type'] ?? 'business' }}" style="border-left: 4px solid {{ $table['color'] ?? 'var(--primary)' }}; cursor: pointer; transition: all 0.2s ease;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                <div>
                    <h3 style="margin: 0; color: var(--dark); font-size: 1.125rem; font-weight: 600;">{{ $table['name'] }}</h3>
                    <p style="margin: 0.25rem 0 0 0; color: var(--text-muted); font-size: 0.875rem;">
                        📊 {{ $table['row_count'] ?? 0 }} enregistrements
                    </p>
                </div>
                <span style="background: {{ $table['color'] ?? 'var(--primary)' }}; color: white; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; font-weight: 500;">
                    {{ $table['type'] ?? 'business' }}
                </span>
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.75rem; font-weight: 500;">Colonnes principales:</div>
                <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                    @foreach(array_slice($table['columns'] ?? [], 0, 4) as $column)
                        <span style="background: var(--light); color: var(--text-muted); padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; border: 1px solid var(--border);">
                            {{ $column }}
                        </span>
                    @endforeach
                    @if(count($table['columns'] ?? []) > 4)
                        <span style="background: var(--light); color: var(--text-muted); padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.75rem; border: 1px solid var(--border);">
                            +{{ count($table['columns']) - 4 }}
                        </span>
                    @endif
                </div>
            </div>

            <div style="display: flex; gap: 0.75rem;">
                <a href="{{ route('admin.crud.table', $table['name']) }}" class="btn btn-primary" style="flex: 1; justify-content: center;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                        <circle cx="12" cy="12" r="3"/>
                    </svg>
                    Voir
                </a>
                <a href="{{ route('admin.crud.create', $table['name']) }}" class="btn btn-secondary" style="flex: 1; justify-content: center;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="5" x2="12" y2="19"/>
                        <line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Ajouter
                </a>
                <button onclick="showTableInfo('{{ $table['name'] }}')" class="btn btn-secondary" style="padding: 0.625rem;">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"/>
                        <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                        <line x1="12" y1="17" x2="12.01" y2="17"/>
                    </svg>
                </button>
            </div>
        </div>
    @endforeach
</div>

@if(empty($tableData))
    <div class="card" style="text-align: center; padding: 3rem;">
        <svg style="width: 64px; height: 64px; margin: 0 auto 1.5rem; color: var(--text-muted);" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
            <polyline points="14 2 14 8 20 8"/>
            <line x1="16" y1="13" x2="8" y2="13"/>
            <line x1="16" y1="17" x2="8" y2="17"/>
            <polyline points="10 9 9 9 8 9"/>
        </svg>
        <h3 style="color: var(--dark); margin-bottom: 0.5rem;">Aucune table trouvée</h3>
        <p style="color: var(--text-muted);">Vérifiez la connexion à la base de données</p>
    </div>
@endif

<!-- Table Info Modal -->
<div id="tableInfoModal" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 1000; backdrop-filter: blur(4px);">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 2rem; border-radius: 1rem; max-width: 600px; width: 90%; max-height: 80vh; overflow-y: auto; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border);">
            <h3 id="modalTitle" style="margin: 0; color: var(--dark); font-size: 1.25rem; font-weight: 600;">Informations de la table</h3>
            <button onclick="closeTableInfo()" class="btn btn-secondary" style="padding: 0.5rem;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div id="modalContent">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<style>
.table-card.hidden {
    display: none;
}

.table-card:hover {
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
}
</style>

<script>
function filterTables(searchTerm) {
    const cards = document.querySelectorAll('.table-card');
    const term = searchTerm.toLowerCase();
    
    cards.forEach(card => {
        const tableName = card.dataset.tableName.toLowerCase();
        if (tableName.includes(term)) {
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
        }
    });
}

function filterByType(type) {
    const cards = document.querySelectorAll('.table-card');
    
    cards.forEach(card => {
        if (!type || card.dataset.tableType === type) {
            card.classList.remove('hidden');
        } else {
            card.classList.add('hidden');
        }
    });
}

function showTableInfo(tableName) {
    // Simuler loading - en réalité, ferait un appel AJAX
    const modal = document.getElementById('tableInfoModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalContent = document.getElementById('modalContent');
    
    modalTitle.textContent = `Table: ${tableName}`;
    modalContent.innerHTML = `
        <div style="margin-bottom: 1rem;">
            <strong>Nom de la table:</strong> ${tableName}
        </div>
        <div style="margin-bottom: 1rem;">
            <strong>Nombre d'enregistrements:</strong> <span id="rowCount">Chargement...</span>
        </div>
        <div style="margin-bottom: 1rem;">
            <strong>Structure:</strong>
            <div id="tableStructure" style="margin-top: 0.5rem;">
                <div style="color: #7f8c8d;">Chargement de la structure...</div>
            </div>
        </div>
    `;
    
    modal.style.display = 'block';
    
    // Simuler un appel AJAX pour obtenir les infos
    setTimeout(() => {
        document.getElementById('rowCount').textContent = Math.floor(Math.random() * 1000);
        document.getElementById('tableStructure').innerHTML = `
            <div style="background: #f8f9fa; padding: 1rem; border-radius: 4px; font-family: monospace; font-size: 0.85rem;">
                id (INT, PRIMARY KEY)<br>
                name (VARCHAR)<br>
                email (VARCHAR, UNIQUE)<br>
                created_at (TIMESTAMP)<br>
                updated_at (TIMESTAMP)
            </div>
        `;
    }, 500);
}

function closeTableInfo() {
    document.getElementById('tableInfoModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('tableInfoModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeTableInfo();
    }
});
</script>
@endsection
