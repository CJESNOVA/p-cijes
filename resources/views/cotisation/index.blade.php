<x-app-layout title="Gestion des cotisations" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
            <h2 class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl">
                Gestion des cotisations
            </h2>
            <div class="hidden h-full py-1 sm:flex">
                <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
            </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
                
                @if(session('success'))
                    <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">{{ session('success') }}</div>
                @endif

                @if(session('error'))
                    <div class="alert flex rounded-lg bg-danger px-4 py-4 text-white sm:px-5">{{ session('error') }}</div>
                @endif

                @if(session('info'))
                    <div class="alert flex rounded-lg bg-info px-4 py-4 text-white sm:px-5">{{ session('info') }}</div>
                @endif

                <!-- Affichage du solde KOBO -->
                @php
                    $userId = Auth::id();
                    $membre = \App\Models\Membre::where('user_id', $userId)->first();
                    $ressourceKOBO = $membre ? \App\Models\Ressourcecompte::where('membre_id', $membre->id)
                                                ->where('ressourcetype_id', 1)
                                                ->where('etat', true)
                                                ->first() : null;
                @endphp
                
                @if($ressourceKOBO)
                    <div class="alert flex rounded-lg bg-info px-4 py-4 text-white sm:px-5">
                        <div class="flex items-center justify-between w-full">
                            <div class="flex items-center">
                                <i class="fas fa-wallet mr-3"></i>
                                <span class="font-medium">Votre solde KOBO : </span>
                                <span class="ml-2 font-bold text-lg">{{ number_format($ressourceKOBO->solde, 2) }} KOBO</span>
                            </div>
                            <small class="text-white/80">
                                <i class="fas fa-info-circle mr-1"></i>
                                Les cotisations sont payées automatiquement avec votre solde KOBO
                            </small>
                        </div>
                    </div>
                @else
                    <div class="alert flex rounded-lg bg-warning px-4 py-4 text-white sm:px-5">
                        <i class="fas fa-exclamation-triangle mr-3"></i>
                        <span>Vous n'avez pas de compte KOBO. Veuillez contacter l'administration pour en créer un.</span>
                    </div>
                @endif

                @if($entreprises->isEmpty())
                    <div class="card mt-6">
                        <div class="card-body text-center py-12">
                            <i class="fas fa-receipt text-6xl text-slate-400 mb-6"></i>
                            <h3 class="text-2xl font-bold text-slate-800 dark:text-navy-100 mb-4">
                                Aucune entreprise membre CJES
                            </h3>
                            <p class="text-slate-600 dark:text-navy-400 mb-8">
                                Vous n'avez aucune entreprise qui est membre CJES. Seules les entreprises membres peuvent gérer des cotisations.
                            </p>
                            <div class="flex justify-center">
                                <a href="{{ route('entreprise.create') }}" 
                                   class="btn bg-primary text-white hover:bg-primary-focus px-6 py-3">
                                    <i class="fas fa-plus mr-2"></i>
                                    Ajouter une entreprise
                                </a>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="space-y-6">
                        @foreach($entreprises as $entreprise)
                        <div class="card">
                            <div class="card-body">
                                <div class="flex items-center justify-between mb-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-slate-800 dark:text-navy-100">
                                            {{ $entreprise->nom }}
                                        </h3>
                                        @if($entreprise->est_membre_cijes)
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-success/10 text-success dark:bg-success/20 dark:text-success-light">
                                                <i class="fas fa-star mr-1"></i>Membre CJES
                                            </span>
                                        @endif
                                    </div>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('cotisation.create', $entreprise->id) }}" 
                                           class="btn bg-primary text-white hover:bg-primary-focus text-sm px-3 py-1 rounded-full">
                                            <i class="fas fa-plus mr-1"></i>
                                            Ajouter une cotisation
                                        </a>
                                    </div>
                                </div>

                                    @if($entreprise->cotisations->isNotEmpty())
                                        <div class="is-scrollbar-hidden min-w-full overflow-x-auto">
                                            <table class="is-hoverable w-full text-left">
                                                <thead>
                                                    <tr class="bg-slate-50 dark:bg-navy-800">
                                                        <th class="px-3 py-2 font-medium text-slate-700 dark:text-navy-200">Type</th>
                                                        <th class="px-3 py-2 font-medium text-slate-700 dark:text-navy-200">Montant</th>
                                                        <th class="px-3 py-2 font-medium text-slate-700 dark:text-navy-200">Payé</th>
                                                        <th class="px-3 py-2 font-medium text-slate-700 dark:text-navy-200">Restant</th>
                                                        <th class="px-3 py-2 font-medium text-slate-700 dark:text-navy-200">Échéance</th>
                                                        <th class="px-3 py-2 font-medium text-slate-700 dark:text-navy-200">Statut</th>
                                                        <th class="px-3 py-2 text-center font-medium text-slate-700 dark:text-navy-200">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($entreprise->cotisations as $cotisation)
                                                        <tr class="border-b">
                                                            <td class="px-3 py-2">
                                                                {{ $cotisation->cotisationtype->titre ?? '-' }}
                                                            </td>
                                                            <td class="px-3 py-2">
                                                                {{ number_format($cotisation->montant, 2) }} {{ $cotisation->devise }}
                                                            </td>
                                                            <td class="px-3 py-2">
                                                                {{ number_format($cotisation->montant_paye, 2) }} {{ $cotisation->devise }}
                                                            </td>
                                                            <td class="px-3 py-2">
                                                                {{ number_format($cotisation->montant_restant, 2) }} {{ $cotisation->devise }}
                                                            </td>
                                                            <td class="px-3 py-2">
                                                                {{ $cotisation->date_echeance->format('d/m/Y') }}
                                                            </td>
                                                            <td class="px-3 py-2">
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
                                                            <td class="px-3 py-2">
                                                                <div class="flex justify-center space-x-2">
                                                                    @if($cotisation->statut !== 'paye')
                                                                        <a href="{{ route('cotisation.edit', $cotisation->id) }}" 
                                                                           class="btn bg-info text-white hover:bg-info-focus text-xs px-2 py-1 rounded">
                                                                            <i class="fas fa-edit"></i>
                                                                        </a>
                                                                        <form action="{{ route('cotisation.markAsPaid', $cotisation->id) }}" method="POST" style="display:inline;">
                                                                            @csrf
                                                                            <button type="submit" 
                                                                                    class="btn bg-success text-white hover:bg-success-focus text-xs px-2 py-1 rounded"
                                                                                    onclick="return confirm('Payer cette cotisation avec votre solde KOBO ?')">
                                                                                <i class="fas fa-wallet"></i>
                                                                            </button>
                                                                        </form>
                                                                    @endif
                                                                    <form action="{{ route('cotisation.destroy', $cotisation->id) }}" method="POST" style="display:inline;">
                                                                        @csrf
                                                                        @method('DELETE')
                                                                        <button type="submit" 
                                                                                class="btn bg-danger text-white hover:bg-danger-focus text-xs px-2 py-1 rounded"
                                                                                onclick="return confirm('Supprimer cette cotisation ?')">
                                                                            <i class="fas fa-trash"></i>
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
                                        <div class="text-center py-8 text-slate-600 dark:text-navy-400">
                                            <i class="fas fa-receipt text-4xl mb-4"></i>
                                            <p>Aucune cotisation enregistrée pour cette entreprise.</p>
                                            <a href="{{ route('cotisation.create', $entreprise->id) }}" 
                                               class="btn bg-primary text-white hover:bg-primary-focus mt-4">
                                                <i class="fas fa-plus mr-2"></i>
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
