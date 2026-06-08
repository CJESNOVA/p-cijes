@extends('admin.layouts.app')

@section('title', 'Détails: ' . $tableName . ' #' . $data->id)

@section('page-title', 'Détails de l\'enregistrement')
@section('page-subtitle', 'Table: {{ $tableName }} - ID: #{{ $data->id }}')

@section('content')
<div class="card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
        <div>
            <h2 style="margin: 0; color: var(--dark);">📄 Détails complets</h2>
            <p style="color: var(--text-muted); margin-top: 0.5rem;">
                Visualisation de l'enregistrement #{{ $data->id }} dans la table {{ $tableName }}
            </p>
        </div>
        <div style="display: flex; gap: 0.5rem;">
            <a href="{{ route('admin.crud.edit', [$tableName, $data->id]) }}" class="btn btn-primary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                    <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                </svg>
                Modifier
            </a>
            <a href="{{ route('admin.crud.table', $tableName) }}" class="btn btn-secondary">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
                </svg>
                Retour à la liste
            </a>
        </div>
    </div>

    <!-- Informations principales -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
        <div class="card" style="border-left: 4px solid var(--primary);">
            <div class="card-title" style="color: var(--primary);">📋 Informations générales</div>
            <div style="margin-top: 1rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                    <span style="color: var(--text-muted);">ID:</span>
                    <span style="font-weight: 600; color: var(--dark);">#{{ $data->id }}</span>
                </div>
                @if(isset($data->created_at))
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="color: var(--text-muted);">Créé le:</span>
                        <span style="font-weight: 600; color: var(--dark);">{{ \Carbon\Carbon::parse($data->created_at)->format('d/m/Y H:i') }}</span>
                    </div>
                @endif
                @if(isset($data->updated_at))
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.75rem;">
                        <span style="color: var(--text-muted);">Modifié le:</span>
                        <span style="font-weight: 600; color: var(--dark);">{{ \Carbon\Carbon::parse($data->updated_at)->format('d/m/Y H:i') }}</span>
                    </div>
                @endif
            </div>
        </div>

        @php
            $booleanFields = [];
            $textFields = [];
            $otherFields = [];
            
            foreach ($columns as $column => $info) {
                if ($column === 'id' || str_contains($info['type'], 'timestamp')) continue;
                
                if (str_contains($info['type'], 'tinyint(1)')) {
                    $booleanFields[$column] = $info;
                } elseif (str_contains($info['type'], 'text') || str_contains($info['type'], 'longtext') || str_contains($info['type'], 'mediumtext')) {
                    $textFields[$column] = $info;
                } else {
                    $otherFields[$column] = $info;
                }
            }
        @endphp

        @if(!empty($booleanFields))
            <div class="card" style="border-left: 4px solid var(--success);">
                <div class="card-title" style="color: var(--success);">🔄 États & Statuts</div>
                <div style="margin-top: 1rem;">
                    @foreach($booleanFields as $column => $info)
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                            <span style="color: var(--text-muted);">{{ ucfirst(str_replace('_', ' ', $column)) }}:</span>
                            <span style="font-weight: 600; color: var(--dark);">
                                @if($data->$column == 1)
                                    <span style="color: var(--success);">✅ Actif</span>
                                @else
                                    <span style="color: var(--danger);">❌ Inactif</span>
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Champs texte longs -->
    @if(!empty($textFields))
        <div class="card" style="margin-bottom: 2rem;">
            <div class="card-title" style="color: var(--primary);">📝 Contenus textes</div>
            <div style="margin-top: 1rem;">
                @foreach($textFields as $column => $info)
                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="color: var(--dark); margin-bottom: 0.5rem;">
                            {{ ucfirst(str_replace('_', ' ', $column)) }}
                            @if(str_contains($column, 'description'))
                                <span style="color: var(--primary); font-size: 0.875rem;"> (Description)</span>
                            @endif
                        </h4>
                        <div style="background: var(--light); padding: 1rem; border-radius: 0.5rem; border-left: 3px solid var(--primary); max-height: 200px; overflow-y: auto;">
                            @if($data->$column)
                                {!! nl2br(e($data->$column)) !!}
                            @else
                                <span style="color: var(--text-muted); font-style: italic;">(vide)</span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Autres champs -->
    @if(!empty($otherFields))
        <div class="card">
            <div class="card-title" style="color: var(--primary);">📊 Autres informations</div>
            <div style="margin-top: 1rem;">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1rem;">
                    @foreach($otherFields as $column => $info)
                        @if(!in_array($column, ['id', 'created_at', 'updated_at']))
                            <div style="background: var(--light); padding: 1rem; border-radius: 0.5rem;">
                                <div style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 0.25rem;">
                                    {{ ucfirst(str_replace('_', ' ', $column)) }}
                                </div>
                                <div style="font-weight: 600; color: var(--dark);">
                                    @if(isset($foreignKeyOptions[$column]) && $foreignKeyOptions[$column] && $data->$column)
                                        @php
                                            $foreignValue = $foreignKeyOptions[$column]['options']->where('id', $data->$column)->first();
                                        @endphp
                                        @if($foreignValue)
                                            <span style="background: #e8f4fd; color: #1976d2; padding: 0.25rem 0.5rem; border-radius: 12px; font-size: 0.8rem;">
                                                {{ $foreignValue->display }}
                                            </span>
                                            <br>
                                            <small style="color: var(--text-muted);">ID: {{ $data->$column }} ({{ $foreignKeyOptions[$column]['table'] }})</small>
                                        @else
                                            <span style="color: var(--danger);">#{{ $data->$column }} (non trouvé)</span>
                                        @endif
                                    @elseif(str_contains($info['type'], 'date'))
                                        @if($data->$column)
                                            @php
                                                try {
                                                    $date = \Carbon\Carbon::parse($data->$column);
                                                    echo $date->format('d/m/Y');
                                                    if(str_contains($info['type'], 'datetime')) {
                                                        echo $date->format(' H:i');
                                                    }
                                                } catch(\Exception $e) {
                                                    echo $data->$column;
                                                }
                                            @endphp
                                        @else
                                            <span style="color: var(--text-muted); font-style: italic;">(non défini)</span>
                                        @endif
                                    @elseif(str_contains($info['type'], 'decimal'))
                                        @if($data->$column)
                                            {{ number_format($data->$column, 2) }}
                                        @else
                                            <span style="color: var(--text-muted); font-style: italic;">(non défini)</span>
                                        @endif
                                    @else
                                        @if($data->$column)
                                            {{ $data->$column }}
                                        @else
                                            <span style="color: var(--text-muted); font-style: italic;">(vide)</span>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    <!-- Actions -->
    <div style="display: flex; gap: 1rem; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid var(--border);">
        <a href="{{ route('admin.crud.edit', [$tableName, $data->id]) }}" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
            </svg>
            Modifier cet enregistrement
        </a>
        
        <form method="POST" action="{{ route('admin.crud.destroy', [$tableName, $data->id]) }}" 
              style="display: inline;" 
              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet enregistrement ? Cette action est irréversible.')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="3 6 5 6 21 6"/>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>
                </svg>
                Supprimer
            </button>
        </form>
        
        <a href="{{ route('admin.crud.table', $tableName) }}" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/>
            </svg>
            Retour à la liste
        </a>
    </div>
</div>
@endsection
