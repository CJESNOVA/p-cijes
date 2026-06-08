@extends('admin.layouts.app')

@section('title', ($isEdit ? 'Modifier' : 'Ajouter') . ' - ' . $tableName)

@section('content')
<div class="card">
    <div style="margin-bottom: 1.5rem;">
        <h2>
            {{ $isEdit ? '✏️ Modifier' : '➕ Ajouter' }} un enregistrement
        </h2>
        <p style="color: #7f8c8d; margin-top: 0.5rem;">
            Table: <strong>{{ $tableName }}</strong>
            @if($isEdit)
                - ID: #{{ $data->id }}
            @endif
        </p>
    </div>

    <form method="POST" action="{{ $isEdit ? route('admin.crud.update', [$tableName, $data->id]) : route('admin.crud.store', $tableName) }}">
        @csrf
        @if($isEdit)
            @method('PUT')
        @endif

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
            <!-- Main Form Fields -->
            <div>
                @foreach($columns as $column => $info)
                    @if(!in_array($column, ['id', 'created_at', 'updated_at', 'password', 'remember_token']) && !str_contains($info['type'], 'timestamp') && !str_contains($info['type'], 'tinyint(1)'))
                        <div class="form-group">
                            <label for="{{ $column }}" class="form-label">
                                {{ ucfirst(str_replace('_', ' ', $column)) }}
                                @if($info['nullable'] == 'NO')
                                    <span style="color: #e74c3c;">*</span>
                                @endif
                            </label>

                            @if($info['type'] == 'boolean' || str_contains($column, 'is_') || str_contains($column, 'has_'))
                                <select id="{{ $column }}" name="{{ $column }}" class="form-select">
                                    <option value="1" {{ ($isEdit && $data->$column) ? 'selected' : '' }}>Oui</option>
                                    <option value="0" {{ ($isEdit && !$data->$column) ? 'selected' : '' }}>Non</option>
                                </select>

                            @elseif(str_contains($column, 'email'))
                                <input type="email" 
                                       id="{{ $column }}" 
                                       name="{{ $column }}" 
                                       value="{{ $isEdit ? $data->$column : old($column) }}" 
                                       class="form-input"
                                       {{ $info['nullable'] == 'NO' ? 'required' : '' }}>

                            @elseif(str_contains($column, 'password'))
                                <input type="password" 
                                       id="{{ $column }}" 
                                       name="{{ $column }}" 
                                       class="form-input"
                                       placeholder="{{ $isEdit ? 'Laisser vide pour ne pas modifier' : '' }}">

                            @elseif(str_contains($info['type'], 'date'))
                                @php
                                    $dateValue = '';
                                    if($isEdit && $data->$column) {
                                        try {
                                            $date = \Carbon\Carbon::parse($data->$column);
                                            $dateValue = str_contains($info['type'], 'datetime') 
                                                ? $date->format('Y-m-d\TH:i') 
                                                : $date->format('Y-m-d');
                                        } catch(\Exception $e) {
                                            $dateValue = old($column);
                                        }
                                    } else {
                                        $dateValue = old($column);
                                    }
                                @endphp
                                @if(str_contains($info['type'], 'datetime'))
                                    <input type="datetime-local" 
                                           id="{{ $column }}" 
                                           name="{{ $column }}" 
                                           value="{{ $dateValue }}" 
                                           class="form-input">
                                @else
                                    <input type="date" 
                                           id="{{ $column }}" 
                                           name="{{ $column }}" 
                                           value="{{ $dateValue }}" 
                                           class="form-input">
                                @endif

                            @elseif(str_contains($column, 'phone') || str_contains($column, 'tel'))
                                <input type="tel" 
                                       id="{{ $column }}" 
                                       name="{{ $column }}" 
                                       value="{{ $isEdit ? $data->$column : old($column) }}" 
                                       class="form-input">

                            @elseif(str_contains($info['type'], 'longtext'))
                                @if(str_contains($column, 'description'))
                                    <textarea id="{{ $column }}" 
                                              name="{{ $column }}" 
                                              rows="8" 
                                              class="form-textarea"
                                              placeholder="Description détaillée..."
                                              {{ $info['nullable'] == 'NO' ? 'required' : '' }}>{{ $isEdit ? $data->$column : old($column) }}</textarea>
                                @else
                                    <textarea id="{{ $column }}" 
                                              name="{{ $column }}" 
                                              rows="4" 
                                              class="form-textarea"
                                              placeholder="Texte..."
                                              {{ $info['nullable'] == 'NO' ? 'required' : '' }}>{{ $isEdit ? $data->$column : old($column) }}</textarea>
                                @endif
                            @elseif(str_contains($info['type'], 'text'))
                                @if(str_contains($column, 'description'))
                                    <textarea id="{{ $column }}" 
                                              name="{{ $column }}" 
                                              rows="6" 
                                              class="form-textarea"
                                              placeholder="Description..."
                                              {{ $info['nullable'] == 'NO' ? 'required' : '' }}>{{ $isEdit ? $data->$column : old($column) }}</textarea>
                                @else
                                    <textarea id="{{ $column }}" 
                                              name="{{ $column }}" 
                                              rows="4" 
                                              class="form-textarea"
                                              placeholder="Texte..."
                                              {{ $info['nullable'] == 'NO' ? 'required' : '' }}>{{ $isEdit ? $data->$column : old($column) }}</textarea>
                                @endif
                            @elseif(str_contains($info['type'], 'varchar') || str_contains($info['type'], 'char'))
                                <input type="text" 
                                       id="{{ $column }}" 
                                       name="{{ $column }}" 
                                       value="{{ $isEdit ? $data->$column : old($column) }}" 
                                       class="form-input"
                                       {{ $info['nullable'] == 'NO' ? 'required' : '' }}>

                            @elseif(str_contains($info['type'], 'bigint'))
                                @if(isset($foreignKeyOptions[$column]) && $foreignKeyOptions[$column])
                                    <!-- Clé étrangère avec options -->
                                    @if($foreignKeyOptions[$column]['options']->count() <= 5)
                                        <!-- Radio buttons pour peu d'options -->
                                        <div style="display: flex; flex-direction: column; gap: 0.5rem; margin-top: 0.5rem;">
                                            @foreach($foreignKeyOptions[$column]['options'] as $option)
                                                <label style="display: flex; align-items: center; cursor: pointer; padding: 0.5rem; border: 1px solid #ddd; border-radius: 4px; transition: background-color 0.2s;" 
                                                       onmouseover="this.style.backgroundColor='#f8f9fa'" 
                                                       onmouseout="this.style.backgroundColor='transparent'">
                                                    <input type="radio" 
                                                           name="{{ $column }}" 
                                                           value="{{ $option->id }}"
                                                           {{ ($isEdit && $data->$column == $option->id) || (!$isEdit && $loop->first) ? 'checked' : '' }}
                                                           style="margin-right: 0.75rem;">
                                                    <span>{{ $option->display }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @else
                                        <!-- Select pour beaucoup d'options -->
                                        <select id="{{ $column }}" name="{{ $column }}" class="form-select" {{ $info['nullable'] == 'NO' ? 'required' : '' }}>
                                            <option value="">Sélectionner...</option>
                                            @foreach($foreignKeyOptions[$column]['options'] as $option)
                                                <option value="{{ $option->id }}" {{ $isEdit && $data->$column == $option->id ? 'selected' : '' }}>
                                                    {{ $option->display }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @endif
                                    <small style="color: #7f8c8d; font-size: 0.8rem;">
                                        📋 Table: {{ $foreignKeyOptions[$column]['table'] }}
                                    </small>
                                @else
                                    <!-- Bigint sans clé étrangère = input number -->
                                    <input type="number" 
                                           id="{{ $column }}" 
                                           name="{{ $column }}" 
                                           value="{{ $isEdit ? $data->$column : old($column) }}" 
                                           class="form-input"
                                           {{ $info['nullable'] == 'NO' ? 'required' : '' }}>
                                @endif
                            @elseif(str_contains($info['type'], 'int') || str_contains($info['type'], 'decimal') || str_contains($info['type'], 'float') || str_contains($info['type'], 'smallint'))
                                <input type="number" 
                                       id="{{ $column }}" 
                                       name="{{ $column }}" 
                                       value="{{ $isEdit ? $data->$column : old($column) }}" 
                                       class="form-input"
                                       {{ $info['nullable'] == 'NO' ? 'required' : '' }}>

                            @elseif(str_contains($info['type'], 'mediumtext'))
                                <input type="text" 
                                       id="{{ $column }}" 
                                       name="{{ $column }}" 
                                       value="{{ $isEdit ? $data->$column : old($column) }}" 
                                       class="form-input"
                                       {{ $info['nullable'] == 'NO' ? 'required' : '' }}>
                            @else
                                <input type="text" 
                                       id="{{ $column }}" 
                                       name="{{ $column }}" 
                                       value="{{ $isEdit ? $data->$column : old($column) }}" 
                                       class="form-input"
                                       {{ $info['nullable'] == 'NO' ? 'required' : '' }}>
                            @endif

                            @if($info['default'])
                                <small style="color: #7f8c8d; display: block; margin-top: 0.25rem;">
                                    Valeur par défaut: {{ $info['default'] }}
                                </small>
                            @endif
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Help Panel -->
            <div>
                <div class="card" style="background: #f8f9fa; border: 1px solid #e1e8ed;">
                    <h3 style="margin: 0 0 1rem 0; color: #2c3e50;">
                        💡 Conseils de saisie
                    </h3>
                    <ul style="margin: 0; padding-left: 1.5rem; color: #7f8c8d;">
                        <li style="margin-bottom: 0.5rem;">Les champs avec <span style="color: #e74c3c;">*</span> sont obligatoires</li>
                        <li style="margin-bottom: 0.5rem;">Utilisez des formats standards pour les dates</li>
                        <li style="margin-bottom: 0.5rem;">Les emails doivent être valides</li>
                        <li style="margin-bottom: 0.5rem;">Les champs numériques acceptent les décimales si nécessaire</li>
                    </ul>
                </div>

                @if($isEdit)
                    <div class="card" style="background: #e3f2fd; border: 1px solid #2196f3; margin-top: 1rem;">
                        <h3 style="margin: 0 0 1rem 0; color: #1976d2;">
                            📝 Informations
                        </h3>
                        <div style="color: #1976d2;">
                            <div style="margin-bottom: 0.5rem;">
                                <strong>ID:</strong> #{{ $data->id }}
                            </div>
                            <div style="margin-bottom: 0.5rem;">
                                <strong>Créé le:</strong> {{ $data->created_at ? \Carbon\Carbon::parse($data->created_at)->format('d/m/Y H:i') : 'N/A' }}
                            </div>
                            <div>
                                <strong>Modifié le:</strong> {{ $data->updated_at ? \Carbon\Carbon::parse($data->updated_at)->format('d/m/Y H:i') : 'N/A' }}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Table Structure Info -->
                <div class="card" style="background: #fff3e0; border: 1px solid #ff9800; margin-top: 1rem;">
                    <h3 style="margin: 0 0 1rem 0; color: #f57c00;">
                        🏗️ Structure de la table
                    </h3>
                    <div style="font-family: monospace; font-size: 0.85rem; color: #e65100;">
                        @foreach($columns as $column => $info)
                            @if(!in_array($column, ['password', 'remember_token']))
                                <div style="margin-bottom: 0.25rem;">
                                    <strong>{{ $column }}</strong> ({{ $info['type'] }})
                                    @if($info['nullable'] == 'NO')
                                        <span style="color: #e74c3c;">*</span>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 2rem; padding-top: 1.5rem; border-top: 1px solid #e1e8ed;">
            <div>
                <a href="{{ route('admin.crud.table', $tableName) }}" class="btn btn-secondary">
                    ← Annuler
                </a>
            </div>
            <div style="display: flex; gap: 0.5rem;">
                @if($isEdit)
                    <a href="{{ route('admin.crud.create', $tableName) }}" class="btn btn-secondary">
                        ➕ Nouveau
                    </a>
                @endif
                <button type="submit" class="btn">
                    {{ $isEdit ? '💾 Mettre à jour' : '➕ Créer' }}
                </button>
            </div>
        </div>
    </form>
</div>

<style>
.form-label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: bold;
    color: #2c3e50;
}

.form-input,
.form-select,
.form-textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: 4px;
    font-size: 1rem;
    transition: border-color 0.3s;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
}

.form-textarea {
    resize: vertical;
    min-height: 100px;
}
</style>
@endsection
