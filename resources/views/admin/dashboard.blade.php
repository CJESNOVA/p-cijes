@extends('admin.layouts.app')

@section('title', 'Dashboard Admin')

@section('page-title', 'Dashboard Admin')
@section('page-subtitle', 'Bienvenue, ' . $admin->name . ' !')

@section('content')
<!-- Stats Cards -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card" style="border-left: 4px solid var(--primary);">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div class="card-title" style="color: var(--primary); margin-bottom: 0.5rem;">🗄️ Tables</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--dark);">{{ $tableCount }}</div>
                <div style="color: var(--text-muted); font-size: 0.875rem;">Tables disponibles</div>
            </div>
            <div style="width: 48px; height: 48px; background: rgba(37, 99, 235, 0.1); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--primary)" stroke-width="2">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                    <polyline points="14 2 14 8 20 8"/>
                    <line x1="16" y1="13" x2="8" y2="13"/>
                    <line x1="16" y1="17" x2="8" y2="17"/>
                    <polyline points="10 9 9 9 8 9"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="card" style="border-left: 4px solid var(--success);">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div class="card-title" style="color: var(--success); margin-bottom: 0.5rem;">⚡ CRUD</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--dark);">Actif</div>
                <div style="color: var(--text-muted); font-size: 0.875rem;">Système opérationnel</div>
            </div>
            <div style="width: 48px; height: 48px; background: rgba(34, 197, 94, 0.1); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--success)" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="card" style="border-left: 4px solid var(--warning);">
        <div style="display: flex; align-items: center; justify-content: space-between;">
            <div>
                <div class="card-title" style="color: var(--warning); margin-bottom: 0.5rem;">🔐 Sécurité</div>
                <div style="font-size: 2rem; font-weight: 700; color: var(--dark);">OK</div>
                <div style="color: var(--text-muted); font-size: 0.875rem;">Authentification active</div>
            </div>
            <div style="width: 48px; height: 48px; background: rgba(245, 158, 11, 0.1); border-radius: 0.5rem; display: flex; align-items: center; justify-content: center;">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--warning)" stroke-width="2">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Welcome Card -->
<div class="card">
    <div class="card-title">👋 Bienvenue dans l'administration CJES</div>
    <p style="color: var(--text-muted); margin-bottom: 1.5rem;">
        Cette interface vous permet de gérer dynamiquement toutes les tables de la base de données via un système CRUD universel et sécurisé.
    </p>
    
    <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
        <a href="{{ route('admin.crud.index') }}" class="btn btn-primary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
            </svg>
            Gérer les tables
        </a>
        <a href="#" class="btn btn-secondary">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"/>
                <path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/>
                <line x1="12" y1="17" x2="12.01" y2="17"/>
            </svg>
            Aide
        </a>
    </div>
</div>

<!-- Features Grid -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
    <div class="card">
        <div class="card-title">🎯 Objectif Principal</div>
        <p style="color: var(--text-muted);">
            Gérer toutes les tables de la base de données sans avoir à créer des controllers et views pour chaque table.
        </p>
        <div style="margin-top: 1rem; padding-top: 1rem; border-top: 1px solid var(--border);">
            <div style="display: flex; align-items: center; gap: 0.5rem; color: var(--success); font-weight: 500;">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="20 6 9 17 4 12"/>
                </svg>
                CRUD universel implémenté
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-title">🚀 Fonctionnalités</div>
        <ul style="color: var(--text-muted); margin: 0; padding-left: 1.25rem;">
            <li style="margin-bottom: 0.5rem;">Liste automatique des tables</li>
            <li style="margin-bottom: 0.5rem;">Formulaires dynamiques</li>
            <li style="margin-bottom: 0.5rem;">Validation intelligente</li>
            <li style="margin-bottom: 0.5rem;">Pagination et recherche</li>
            <li>Sécurité renforcée</li>
        </ul>
    </div>

    <div class="card">
        <div class="card-title">📊 Statut Actuel</div>
        <div style="display: flex; flex-direction: column; gap: 0.75rem;">
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div style="width: 8px; height: 8px; background: var(--success); border-radius: 50%;"></div>
                <span style="color: var(--text-muted);">Infrastructure admin opérationnelle</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div style="width: 8px; height: 8px; background: var(--success); border-radius: 50%;"></div>
                <span style="color: var(--text-muted);">CRUD dynamique fonctionnel</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div style="width: 8px; height: 8px; background: var(--success); border-radius: 50%;"></div>
                <span style="color: var(--text-muted);">Design moderne intégré</span>
            </div>
            <div style="display: flex; align-items: center; gap: 0.5rem;">
                <div style="width: 8px; height: 8px; background: var(--warning); border-radius: 50%;"></div>
                <span style="color: var(--text-muted);">Tests en cours</span>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="card">
    <div class="card-title">⚡ Actions Rapides</div>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-top: 1rem;">
        <a href="{{ route('admin.crud.index') }}" class="btn btn-primary" style="justify-content: center;">
            🗄️ CRUD Dynamique
        </a>
        <a href="#" class="btn btn-secondary" style="justify-content: center;">
            � Statistiques
        </a>
        <a href="#" class="btn btn-secondary" style="justify-content: center;">
            ⚙️ Paramètres
        </a>
        <form action="{{ route('admin.logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" class="btn btn-danger" style="width: 100%; justify-content: center;">
                🚪 Déconnexion
            </button>
        </form>
    </div>
</div>
@endsection
