<x-app-layout title="Mes demandes SIKA" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-[#4FBE96] to-[#4FBE96] flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Mes demandes SIKA
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Consultez l'historique de vos demandes et les documents associés
                    </p>
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

                <!-- Bouton d'action -->
                <div class="mb-6">
                    <a href="{{ route('ressourcecompte.createDemande') }}" 
                       class="btn bg-primary text-white hover:bg-primary-focus">
                        <i class="fas fa-plus mr-2"></i>
                        Nouvelle demande SIKA
                    </a>
                </div>

                <!-- Liste des demandes -->
                @if($demandes->isNotEmpty())
                    <div class="card">
                        <div class="is-scrollbar-hidden min-w-full overflow-x-auto">
                            <table class="is-hoverable w-full text-left">
                                <thead>
                                    <tr class="bg-slate-50 dark:bg-navy-800">
                                        <th class="px-3 py-2 font-medium text-slate-700 dark:text-navy-200">Date</th>
                                        <th class="px-3 py-2 font-medium text-slate-700 dark:text-navy-200">Type de document</th>
                                        <th class="px-3 py-2 font-medium text-slate-700 dark:text-navy-200">Titre</th>
                                        <th class="px-3 py-2 font-medium text-slate-700 dark:text-navy-200">Fichier</th>
                                        <th class="px-3 py-2 font-medium text-slate-700 dark:text-navy-200">Statut</th>
                                        <th class="px-3 py-2 text-center font-medium text-slate-700 dark:text-navy-200">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($demandes as $demande)
                                        <tr class="border-b">
                                            <td class="px-3 py-2">
                                                {{ $demande->datedemande->format('d/m/Y H:i') }}
                                            </td>
                                            <td class="px-3 py-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                                    {{ $demande->demandetype->titre ?? '-' }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2">
                                                {{ $demande->titre }}
                                            </td>
                                            <td class="px-3 py-2">
                                                @if($demande->fichier)
                                                    <div class="flex items-center space-x-2">
                                                        <i class="fas fa-file-pdf text-red-500"></i>
                                                        <span class="text-sm">{{ basename($demande->fichier) }}</span>
                                                    </div>
                                                @else
                                                    <span class="text-gray-400 text-sm">-</span>
                                                @endif
                                            </td>
                                            <td class="px-3 py-2">
                                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                                    {{ $demande->etat == 1 ? 'bg-success/10 text-success' : 'bg-warning/10 text-warning' }}">
                                                    <i class="fas {{ $demande->etat == 1 ? 'fa-check-circle' : 'fa-clock' }} mr-1"></i>
                                                    {{ $demande->etat == 1 ? 'Actif' : 'En attente' }}
                                                </span>
                                            </td>
                                            <td class="px-3 py-2">
                                                <div class="flex justify-center space-x-2">
                                                    @if($demande->fichier)
                                                        <a href="{{ route('demande.download', $demande->id) }}" 
                                                           class="btn bg-info text-white hover:bg-info-focus text-xs px-2 py-1 rounded"
                                                           title="Télécharger le fichier">
                                                            <i class="fas fa-download"></i>
                                                        </a>
                                                    @endif
                                                    <button onclick="viewDetails({{ $demande->id }})" 
                                                            class="btn bg-primary text-white hover:bg-primary-focus text-xs px-2 py-1 rounded"
                                                            title="Voir les détails">
                                                        <i class="fas fa-eye"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @else
                    <div class="card">
                        <div class="card-body text-center py-12">
                            <i class="fas fa-file-alt text-6xl text-slate-400 mb-6"></i>
                            <h3 class="text-2xl font-bold text-slate-800 dark:text-navy-100 mb-4">
                                Aucune demande SIKA
                            </h3>
                            <p class="text-slate-600 dark:text-navy-400 mb-8">
                                Vous n'avez pas encore soumis de demande SIKA avec des documents.
                            </p>
                            <a href="{{ route('ressourcecompte.createDemande') }}" 
                               class="btn bg-primary text-white hover:bg-primary-focus">
                                <i class="fas fa-plus mr-2"></i>
                                Faire une demande SIKA
                            </a>
                        </div>
                    </div>
                @endif
            </div>

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    
        </div>
    </main>
</x-app-layout>

<script>
function viewDetails(demandeId) {
    // Implémenter la modal ou la redirection vers les détails
    alert('Fonctionnalité de détails à implémenter pour la demande #' + demandeId);
}
</script>
