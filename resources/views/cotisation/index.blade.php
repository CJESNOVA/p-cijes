<x-app-layout title="Gestion des cotisations" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center justify-between mb-2">
                <div class="flex items-center gap-4">
                    <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                            Gestion des cotisations
                        </h1>
                        <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                            Suivez et gérez les cotisations de vos entreprises membres CJES
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
                <!-- Messages modernes -->
                @if(session('success'))
                    <div class="alert flex rounded-lg bg-green-500 px-6 py-4 text-white mb-6 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert flex rounded-lg bg-red-500 px-6 py-4 text-white mb-6 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('error') }}
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert flex rounded-lg bg-blue-500 px-6 py-4 text-white mb-6 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('info') }}
                    </div>
                @endif

                <!-- Solde KOBO moderne -->
                @php
                    $userId = Auth::id();
                    $membre = \App\Models\Membre::where('user_id', $userId)->first();
                    $ressourceKOBO = $membre ? \App\Models\Ressourcecompte::where('membre_id', $membre->id)
                                                ->where('ressourcetype_id', 1)
                                                ->where('etat', true)
                                                ->first() : null;
                @endphp
                
                @if($ressourceKOBO)
                    <div class="card shadow-xl border-0 bg-gradient-to-br from-emerald-500 to-emerald-600 mb-6">
                        <div class="card-body p-6 text-white">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <div class="h-12 w-12 rounded-xl bg-white/20 flex items-center justify-center mr-4">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-emerald-100 text-sm font-medium">Votre solde KOBO</p>
                                        <p class="text-3xl font-bold mt-1">{{ number_format($ressourceKOBO->solde, 2) }} KOBO</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-emerald-100 text-sm">
                                        <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Paiement automatique
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="card shadow-xl border-0 bg-gradient-to-br from-amber-500 to-amber-600 mb-6">
                        <div class="card-body p-6 text-white">
                            <div class="flex items-center">
                                <div class="h-12 w-12 rounded-xl bg-white/20 flex items-center justify-center mr-4">
                                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-amber-100 font-medium">Aucun compte KOBO</p>
                                    <p class="text-sm text-amber-200 mt-1">Veuillez contacter l'administration pour en créer un</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Statistiques -->
                @if($entreprises->isNotEmpty())
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="card shadow-xl border-0 bg-gradient-to-br from-blue-500 to-blue-600">
                            <div class="card-body p-6 text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-blue-100 text-sm font-medium">Total cotisations</p>
                                        <p class="text-3xl font-bold mt-1">
                                            {{ $entreprises->sum(function($e) { return $e->cotisations->count(); }) }}
                                        </p>
                                    </div>
                                    <div class="h-12 w-12 rounded-xl bg-white/20 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-xl border-0 bg-gradient-to-br from-green-500 to-green-600">
                            <div class="card-body p-6 text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-green-100 text-sm font-medium">Montant total payé</p>
                                        <p class="text-3xl font-bold mt-1">
                                            {{ number_format($entreprises->sum(function($e) { return $e->cotisations->sum('montant_paye'); }), 0) }} XOF
                                        </p>
                                    </div>
                                    <div class="h-12 w-12 rounded-xl bg-white/20 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card shadow-xl border-0 bg-gradient-to-br from-amber-500 to-amber-600">
                            <div class="card-body p-6 text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-amber-100 text-sm font-medium">En attente</p>
                                        <p class="text-3xl font-bold mt-1">
                                            {{ $entreprises->sum(function($e) { return $e->cotisations->where('statut', '!=', 'paye')->count(); }) }}
                                        </p>
                                    </div>
                                    <div class="h-12 w-12 rounded-xl bg-white/20 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Liste des entreprises -->
                @if($entreprises->isEmpty())
                    <!-- État vide moderne -->
                    <div class="card shadow-xl">
                        <div class="card-body p-12 text-center">
                            <div class="h-20 w-20 rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h-1m2-5h-8"></path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-semibold text-slate-800 dark:text-navy-50 mb-2">
                                Aucune entreprise membre CJES
                            </h3>
                            <p class="text-slate-600 dark:text-navy-200 mb-6">
                                Vous n'avez aucune entreprise qui est membre CJES. Seules les entreprises membres peuvent gérer des cotisations.
                            </p>
                            <a href="{{ route('entreprise.create') }}" 
                               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-medium rounded-lg hover:from-emerald-600 hover:to-emerald-700 transition-all shadow-lg">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Ajouter une entreprise
                            </a>
                        </div>
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach($entreprises as $entreprise)
                            <div class="card shadow-xl hover:shadow-2xl transition-all duration-300 hover:-translate-y-1">
                                <div class="card-body p-6">
                                    <!-- Header de l'entreprise -->
                                    <div class="flex items-center justify-between mb-6">
                                        <div class="flex items-center gap-4">
                                            <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center">
                                                <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h-1m2-5h-8"></path>
                                                </svg>
                                            </div>
                                            <div>
                                                <h3 class="text-lg font-semibold text-slate-800 dark:text-navy-50">
                                                    {{ $entreprise->nom }}
                                                </h3>
                                                @if($entreprise->est_membre_cijes)
                                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.005 1.603-.921 1.902 0l1.07-3.292a1 1 0 00-.95-.69H8.084a1 1 0 00-.95.69l-2.8 2.034c-.783.57-1.838.197-1.588 1.81l2.8 2.034a1 1 0 00.364 1.118L9.049 2.927z"></path>
                                                        </svg>
                                                        Membre CJES
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <a href="{{ route('cotisation.create', $entreprise->id) }}" 
                                           class="inline-flex items-center px-4 py-2 bg-emerald-500 text-white text-sm font-medium rounded-lg hover:bg-emerald-600 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                            Ajouter une cotisation
                                        </a>
                                    </div>

                                    <!-- Cotisations -->
                                    @if($entreprise->cotisations->isNotEmpty())
                                        <div class="overflow-x-auto">
                                            <table class="w-full">
                                                <thead>
                                                    <tr class="border-b border-slate-200 dark:border-navy-500">
                                                        <th class="text-left py-3 px-4 text-sm font-semibold text-slate-700 dark:text-navy-100">Type</th>
                                                        <th class="text-left py-3 px-4 text-sm font-semibold text-slate-700 dark:text-navy-100">Montant</th>
                                                        <th class="text-left py-3 px-4 text-sm font-semibold text-slate-700 dark:text-navy-100">Payé</th>
                                                        <th class="text-left py-3 px-4 text-sm font-semibold text-slate-700 dark:text-navy-100">Restant</th>
                                                        <th class="text-left py-3 px-4 text-sm font-semibold text-slate-700 dark:text-navy-100">Échéance</th>
                                                        <th class="text-left py-3 px-4 text-sm font-semibold text-slate-700 dark:text-navy-100">Statut</th>
                                                        <th class="text-center py-3 px-4 text-sm font-semibold text-slate-700 dark:text-navy-100">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($entreprise->cotisations as $cotisation)
                                                        <tr class="border-b border-slate-100 dark:border-navy-600 hover:bg-slate-50 dark:hover:bg-navy-700/50 transition-colors">
                                                            <td class="py-3 px-4">
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-400">
                                                                    {{ $cotisation->cotisationtype->titre ?? '-' }}
                                                                </span>
                                                            </td>
                                                            <td class="py-3 px-4 font-medium text-slate-800 dark:text-navy-50">
                                                                {{ number_format($cotisation->montant, 2) }} {{ $cotisation->devise }}
                                                            </td>
                                                            <td class="py-3 px-4">
                                                                <span class="text-green-600 font-medium">{{ number_format($cotisation->montant_paye, 2) }} {{ $cotisation->devise }}</span>
                                                            </td>
                                                            <td class="py-3 px-4">
                                                                <span class="text-amber-600 font-medium">{{ number_format($cotisation->montant_restant, 2) }} {{ $cotisation->devise }}</span>
                                                            </td>
                                                            <td class="py-3 px-4 text-sm text-slate-600 dark:text-navy-200">
                                                                {{ $cotisation->date_echeance->format('d/m/Y') }}
                                                            </td>
                                                            <td class="py-3 px-4">
                                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                                                    @switch($cotisation->statut)
                                                                        @case('en_attente')
                                                                            bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                                        @break
                                                                        @case('paye')
                                                                            bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                                        @break
                                                                        @case('partiel')
                                                                            bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                                        @break
                                                                        @case('retard')
                                                                            bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                                        @break
                                                                    @endswitch
                                                                ">
                                                                    {{ $cotisation->statut_label }}
                                                                </span>
                                                            </td>
                                                            <td class="py-3 px-4">
                                                                <div class="flex justify-center space-x-2">
                                                                    @if($cotisation->statut !== 'paye')
                                                                        <a href="{{ route('cotisation.edit', $cotisation->id) }}" 
                                                                           class="inline-flex items-center px-3 py-1 bg-blue-500 text-white text-xs font-medium rounded-lg hover:bg-blue-600 transition-colors">
                                                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                            </svg>
                                                                            Modifier
                                                                        </a>
                                                                        <form action="{{ route('cotisation.markAsPaid', $cotisation->id) }}" method="POST" class="inline">
                                                                            @csrf
                                                                            <button type="submit" 
                                                                                    class="inline-flex items-center px-3 py-1 bg-green-500 text-white text-xs font-medium rounded-lg hover:bg-green-600 transition-colors"
                                                                                    onclick="return confirm('Payer cette cotisation avec votre solde KOBO ?')">
                                                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                                                                </svg>
                                                                                Payer
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                    <form action="{{ route('cotisation.destroy', $cotisation->id) }}" method="POST" class="inline">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" 
                                                                                class="inline-flex items-center px-3 py-1 bg-red-500 text-white text-xs font-medium rounded-lg hover:bg-red-600 transition-colors"
                                                                                onclick="return confirm('Supprimer cette cotisation ?')">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                            </svg>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    @else
                                        <!-- État vide pour les cotisations -->
                                        <div class="text-center py-8">
                                            <div class="h-16 w-16 rounded-xl bg-gradient-to-br from-emerald-100 to-emerald-200 flex items-center justify-center mx-auto mb-4">
                                                <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                </svg>
                                            </div>
                                            <h4 class="text-lg font-semibold text-slate-800 dark:text-navy-50 mb-2">
                                                Aucune cotisation
                                            </h4>
                                            <p class="text-slate-600 dark:text-navy-200 mb-4">
                                                Cette entreprise n'a pas encore de cotisation enregistrée
                                            </p>
                                            <a href="{{ route('cotisation.create', $entreprise->id) }}" 
                                               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-medium rounded-lg hover:from-emerald-600 hover:to-emerald-700 transition-all shadow-lg">
                                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                                Ajouter la première cotisation
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    
        </div>
    </main>
</x-app-layout>
