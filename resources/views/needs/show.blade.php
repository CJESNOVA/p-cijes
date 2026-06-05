<x-app-layout title="Détail du besoin" is-sidebar-open="true" is-header-blur="true">

    <main class="main-content w-full px-[var(--margin-x)] pb-8">

        <!-- Header -->
        <div class="mb-10">
            <div class="flex items-center gap-4 mb-8">
                <a href="{{ route('needs.index') }}" class="flex items-center gap-2 text-primary dark:text-accent hover:underline">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Retour
                </a>
            </div>
            <div class="flex items-start justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">{{ $need['category'] ?? 'Sans titre' }}</h1>
                    <div class="mt-3 flex items-center gap-4">
                        <span class="inline-block px-3 py-1 text-sm font-semibold text-white bg-blue-500 rounded-full">
                            {{ $need['priority'] == 1 ? 'Priorité basse' : ($need['priority'] == 2 ? 'Priorité moyenne' : 'Priorité haute') }}
                        </span>
                        @if($need['deadline'])
                            <span class="text-sm text-slate-600 dark:text-navy-300">
                                Clôture: <strong>{{ \Carbon\Carbon::parse($need['deadline'])->format('d/m/Y') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('needs.index') }}"
                   class="btn rounded-lg border border-slate-300 bg-white px-4 py-2.5 font-medium text-slate-700 hover:bg-slate-50 dark:border-navy-450 dark:bg-navy-700 dark:text-navy-100 dark:hover:bg-navy-600">
                    Fermer
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
                <!-- Informations détaillées -->
                <div class="card px-4 pb-4 sm:px-5">
                    <div class="mt-5">
                        <h2 class="text-xl font-bold text-slate-800 dark:text-navy-50 mb-4">Description</h2>
                        <p class="text-slate-700 dark:text-navy-200 leading-relaxed mb-6">
                            {{ $need['description'] ?? 'Pas de description' }}
                        </p>

                        @if($need['profiles'])
                            <div class="mb-6 p-4 bg-blue-50 dark:bg-navy-600 rounded-lg border border-blue-200 dark:border-navy-500">
                                <h3 class="font-semibold text-slate-800 dark:text-navy-50 mb-2">Profils recherchés</h3>
                                <p class="text-slate-700 dark:text-navy-200">{{ $need['profiles'] }}</p>
                            </div>
                        @endif

                        @if($need['conditions'])
                            <div class="mb-6 p-4 bg-green-50 dark:bg-navy-600 rounded-lg border border-green-200 dark:border-navy-500">
                                <h3 class="font-semibold text-slate-800 dark:text-navy-50 mb-2">Conditions d'éligibilité</h3>
                                <p class="text-slate-700 dark:text-navy-200">{{ $need['conditions'] }}</p>
                            </div>
                        @endif

                        @if($need['attachment'])
                            <div class="mb-6 p-4 bg-purple-50 dark:bg-navy-600 rounded-lg border border-purple-200 dark:border-navy-500">
                                <h3 class="font-semibold text-slate-800 dark:text-navy-50 mb-2">Pièce jointe</h3>
                                <a href="{{ asset('storage/' . $need['attachment']) }}" 
                                   target="_blank"
                                   class="inline-flex items-center gap-2 text-primary dark:text-accent hover:underline">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Télécharger
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Candidatures -->
                <div class="card px-4 pb-4 sm:px-5 mt-6">
                    <div class="mt-5">
                        <div class="flex items-center justify-between mb-6">
                            <h2 class="text-xl font-bold text-slate-800 dark:text-navy-50">
                                Candidatures
                                <span class="text-sm font-normal text-slate-600 dark:text-navy-300">
                                    ({{ isset($applications) && is_array($applications) ? count($applications) : 0 }})
                                </span>
                            </h2>
                        </div>

                        @if(isset($applications) && is_array($applications) && count($applications) > 0)
                            <div class="space-y-4">
                                @foreach($applications as $application)
                                    <div class="border border-slate-200 dark:border-navy-500 rounded-lg p-4 hover:bg-slate-50 dark:hover:bg-navy-600 transition">
                                        <div class="flex items-start justify-between mb-3">
                                            <div>
                                                <h4 class="font-semibold text-slate-800 dark:text-navy-50">
                                                    {{ $application['applicant_id'] ?? 'Candidat' }}
                                                </h4>
                                                <p class="text-sm text-slate-600 dark:text-navy-300">
                                                    Statut: 
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
                                            <p class="text-slate-700 dark:text-navy-200 text-sm mb-3">{{ $application['message'] }}</p>
                                        @endif

                                        <div class="grid grid-cols-2 gap-2 text-sm mb-3">
                                            @if($application['expected_amount'])
                                                <div>
                                                    <p class="text-slate-600 dark:text-navy-300">Montant demandé:</p>
                                                    <p class="font-semibold text-slate-800 dark:text-navy-50">{{ number_format($application['expected_amount'], 0, ',', ' ') }} FCFA</p>
                                                </div>
                                            @endif

                                            @if($application['awarded_amount'])
                                                <div>
                                                    <p class="text-slate-600 dark:text-navy-300">Montant attribué:</p>
                                                    <p class="font-semibold text-slate-800 dark:text-navy-50">{{ number_format($application['awarded_amount'], 0, ',', ' ') }} FCFA</p>
                                                </div>
                                            @endif
                                        </div>

                                        @if($application['status'] != 'awarded' && $application['status'] != 'rejected')
                                            <div class="flex gap-2 pt-3 border-t border-slate-200 dark:border-navy-500">
                                                <button type="button"
                                                        onclick="openAwardModal('{{ $application['id'] }}')"
                                                        class="btn rounded-lg bg-green-500 px-3 py-1.5 font-medium text-white hover:bg-green-600 text-sm">
                                                    Attribuer
                                                </button>
                                                <button type="button"
                                                        onclick="rejectApplication('{{ $application['id'] }}')"
                                                        class="btn rounded-lg bg-red-500 px-3 py-1.5 font-medium text-white hover:bg-red-600 text-sm">
                                                    Rejeter
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="inline-block h-12 w-12 text-slate-400 dark:text-navy-400 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="text-slate-600 dark:text-navy-300 text-lg">Aucune candidature pour le moment</p>
                            </div>
                        @endif
                    </div>
                </div>
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
                            <span class="font-medium text-slate-700 dark:text-navy-100">Montant à attribuer</span>
                        </label>
                        <input type="number"
                               name="awarded_amount"
                               step="0.01"
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
            form.action = '{{ route("needs.awardApplication", ["needId" => $need["id"], "applicationId" => "APPLICATION_ID"]) }}'.replace('APPLICATION_ID', applicationId);
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
    </script>

</x-app-layout>
