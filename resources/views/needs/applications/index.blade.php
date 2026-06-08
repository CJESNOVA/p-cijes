<x-app-layout title="Candidatures" is-sidebar-open="true" is-header-blur="true">

    <main class="main-content w-full px-[var(--margin-x)] pb-8">

        <!-- Header -->
        <div class="mb-10">
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('needs.show', $needId) }}" class="flex items-center gap-2 text-primary dark:text-accent hover:underline">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Retour au besoin
                </a>
            </div>
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">Candidatures</h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">Gérez les candidatures pour ce besoin</p>
                </div>
                <a href="{{ route('needs.index') }}"
                   class="btn rounded-lg border border-slate-300 bg-white px-4 py-2.5 font-medium text-slate-700 hover:bg-slate-50 dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:hover:bg-navy-600">
                    Voir tous les besoins
                </a>
            </div>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5 mb-6">
                <svg class="h-5 w-5 flex-none" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                </svg>
                <div class="ml-3">{{ session('success') }}</div>
            </div>
        @endif

        @if(session('error'))
            <div class="alert flex rounded-lg bg-error px-4 py-4 text-white sm:px-5 mb-6">
                <svg class="h-5 w-5 flex-none" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                </svg>
                <div class="ml-3">{{ session('error') }}</div>
            </div>
        @endif

        <div class="grid grid-cols-12 gap-6">
            <!-- Colonne principale -->
            <div class="col-span-12 lg:col-span-8">
                @if(isset($applications) && is_array($applications) && count($applications) > 0)
                    <!-- Filtres de statut -->
                    <div class="mb-6 flex gap-2 flex-wrap">
                        <button class="btn rounded-lg bg-blue-500 px-4 py-2 font-medium text-white hover:bg-blue-600 text-sm" onclick="filterByStatus('all')">
                            Tous ({{ count($applications) }})
                        </button>
                        <button class="btn rounded-lg border border-slate-300 bg-white px-4 py-2 font-medium text-slate-700 hover:bg-slate-50 dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:hover:bg-navy-600 text-sm" onclick="filterByStatus('pending')">
                            En attente
                        </button>
                        <button class="btn rounded-lg border border-slate-300 bg-white px-4 py-2 font-medium text-slate-700 hover:bg-slate-50 dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:hover:bg-navy-600 text-sm" onclick="filterByStatus('awarded')">
                            Attribuées
                        </button>
                        <button class="btn rounded-lg border border-slate-300 bg-white px-4 py-2 font-medium text-slate-700 hover:bg-slate-50 dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:hover:bg-navy-600 text-sm" onclick="filterByStatus('rejected')">
                            Rejetées
                        </button>
                    </div>

                    <!-- Liste des candidatures -->
                    <div class="space-y-4">
                        @foreach($applications as $application)
                            <div class="card px-4 py-4 sm:px-5 application-card" data-status="{{ $application['status'] ?? 'pending' }}">
                                <div class="flex items-start justify-between mb-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-800 dark:text-navy-50">
                                            {{ $application['applicant_id'] ?? 'Candidat' }}
                                        </h3>
                                        <p class="text-sm text-slate-600 dark:text-navy-300 mt-1">
                                            @if($application['status'] == 'awarded')
                                                <span class="inline-block px-2 py-1 text-xs font-semibold text-white bg-green-500 rounded">Attribué</span>
                                            @elseif($application['status'] == 'rejected')
                                                <span class="inline-block px-2 py-1 text-xs font-semibold text-white bg-red-500 rounded">Rejeté</span>
                                            @else
                                                <span class="inline-block px-2 py-1 text-xs font-semibold text-white bg-blue-500 rounded">En attente</span>
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                @if($application['message'])
                                    <div class="mb-4 p-4 bg-slate-50 dark:bg-navy-600 rounded-lg border border-slate-200 dark:border-navy-500">
                                        <p class="text-slate-700 dark:text-navy-200 text-sm">{{ $application['message'] }}</p>
                                    </div>
                                @endif

                                <!-- Informations candidat -->
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3 sm:gap-4 mb-4 text-sm">
                                    @if($application['portfolio_url'])
                                        <div>
                                            <p class="text-slate-600 dark:text-navy-300">Portfolio:</p>
                                            <a href="{{ $application['portfolio_url'] }}" 
                                               target="_blank"
                                               class="text-primary dark:text-accent hover:underline font-medium">
                                                Consulter
                                            </a>
                                        </div>
                                    @endif

                                    @if($application['expected_amount'])
                                        <div>
                                            <p class="text-slate-600 dark:text-navy-300">Montant demandé:</p>
                                            <p class="font-semibold text-slate-800 dark:text-navy-50">
                                                {{ number_format($application['expected_amount'], 0, ',', ' ') }} FCFA
                                            </p>
                                        </div>
                                    @endif

                                    @if($application['awarded_amount'])
                                        <div>
                                            <p class="text-slate-600 dark:text-navy-300">Montant attribué:</p>
                                            <p class="font-semibold text-slate-800 dark:text-navy-50">
                                                {{ number_format($application['awarded_amount'], 0, ',', ' ') }} FCFA
                                            </p>
                                        </div>
                                    @endif
                                </div>

                                <!-- Actions -->
                                @if($application['status'] != 'awarded' && $application['status'] != 'rejected')
                                    <div class="flex gap-2 pt-4 border-t border-slate-200 dark:border-navy-500">
                                        <button type="button"
                                                onclick="openAwardModal('{{ $application['id'] }}')"
                                                class="btn rounded-lg bg-green-500 px-4 py-2 font-medium text-white hover:bg-green-600 text-sm flex-1">
                                            <svg class="inline-block h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Attribuer
                                        </button>
                                        <button type="button"
                                                onclick="rejectApplication('{{ $application['id'] }}')"
                                                class="btn rounded-lg bg-red-500 px-4 py-2 font-medium text-white hover:bg-red-600 text-sm flex-1">
                                            <svg class="inline-block h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Rejeter
                                        </button>
                                    </div>
                                @endif

                                @if($application['notes'])
                                    <div class="mt-4 p-3 bg-yellow-50 dark:bg-navy-600 rounded-lg border border-yellow-200 dark:border-navy-500">
                                        <p class="text-sm text-yellow-800 dark:text-yellow-300"><strong>Notes:</strong> {{ $application['notes'] }}</p>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                @else
                    <div class="card px-4 py-12 sm:px-5 text-center">
                        <svg class="inline-block h-16 w-16 text-slate-400 dark:text-navy-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-slate-600 dark:text-navy-300 text-lg">Aucune candidature pour le moment</p>
                        <p class="text-slate-500 dark:text-navy-400 mt-2">Les candidatures apparaîtront ici quand des personnes postuleront</p>
                    </div>
                @endif
            </div>

            <!-- Sidebar -->
            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>
        </div>
    </main>

    <!-- Modal d'attribution -->
    <div id="awardModal" class="modal hidden fixed inset-0 z-50 flex items-center justify-center bg-slate-950/50">
        <div class="card max-w-sm w-full mx-4">
            <div class="p-5">
                <h3 class="text-lg font-bold text-slate-800 dark:text-navy-50 mb-4">Attribuer cette candidature</h3>
                
                <form id="awardForm" method="POST" onsubmit="submitAward(event)">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-4">
                        <label class="block mb-2">
                            <span class="font-medium text-slate-700 dark:text-navy-100">Montant à attribuer (FCFA)</span>
                        </label>
                        <input type="number"
                               name="awarded_amount"
                               step="0.01"
                               min="0"
                               class="form-input w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:hover:border-navy-400 dark:focus:border-accent"
                               placeholder="0">
                    </div>

                    <div class="mb-6">
                        <label class="block mb-2">
                            <span class="font-medium text-slate-700 dark:text-navy-100">Notes</span>
                        </label>
                        <textarea name="notes"
                                  rows="3"
                                  class="form-textarea w-full rounded-lg border border-slate-300 bg-white p-2.5 placeholder:text-slate-400/70 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:hover:border-navy-400 dark:focus:border-accent"
                                  placeholder="Ajoutez des notes..."></textarea>
                    </div>

                    <div class="flex gap-3 justify-end">
                        <button type="button"
                                onclick="closeAwardModal()"
                                class="btn rounded-lg border border-slate-300 bg-white px-4 py-2 font-medium text-slate-700 hover:bg-slate-50 dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:hover:bg-navy-600">
                            Annuler
                        </button>
                        <button type="submit"
                                class="btn rounded-lg bg-green-500 px-4 py-2 font-medium text-white hover:bg-green-600">
                            Attribuer
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openAwardModal(applicationId) {
            const modal = document.getElementById('awardModal');
            const form = document.getElementById('awardForm');
            form.action = '{{ route("needs.awardApplication", ["needId" => $needId, "applicationId" => "APPLICATION_ID"]) }}'.replace('APPLICATION_ID', applicationId);
            modal.classList.remove('hidden');
        }

        function closeAwardModal() {
            const modal = document.getElementById('awardModal');
            modal.classList.add('hidden');
            document.getElementById('awardForm').reset();
        }

        function submitAward(event) {
            event.preventDefault();
            const form = document.getElementById('awardForm');
            form.submit();
        }

        function rejectApplication(applicationId) {
            if (confirm('Êtes-vous sûr de vouloir rejeter cette candidature ?')) {
                // TODO: Implement rejection functionality
                alert('Fonctionnalité de rejet à implémenter');
            }
        }

        function filterByStatus(status) {
            const cards = document.querySelectorAll('.application-card');
            cards.forEach(card => {
                if (status === 'all' || card.dataset.status === status) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }
    </script>

</x-app-layout>
