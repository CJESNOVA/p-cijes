<x-app-layout title="Ressources utilisées pour les formations" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Ressources utilisées pour les formations
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Suivi des paiements et transactions pour les inscriptions aux formations
                    </p>
                </div>
            </div>
        </div>

        <!-- Cartes statistiques -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <div class="card bg-gradient-to-br from-emerald-500 to-emerald-600 text-white border-0 shadow-xl">
                <div class="card-body p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-emerald-100 text-sm font-medium">Total</p>
                            <p class="text-3xl font-bold mt-2">{{ $ressources->count() }}</p>
                        </div>
                        <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-gradient-to-br from-green-500 to-green-600 text-white border-0 shadow-xl">
                <div class="card-body p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Montant Total</p>
                            <p class="text-3xl font-bold mt-2">{{ number_format($ressources->sum('montant'), 0, ',', ' ') }} FCFA</p>
                        </div>
                        <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card bg-gradient-to-br from-teal-500 to-teal-600 text-white border-0 shadow-xl">
                <div class="card-body p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-teal-100 text-sm font-medium">Payées</p>
                            <p class="text-3xl font-bold mt-2">{{ $ressources->where('montant', '>', 0)->count() }}</p>
                        </div>
                        <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages modernes -->
        @if(session('success'))
            <div class="alert flex rounded-lg bg-green-500 px-6 py-4 text-white mb-6 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Tableau des ressources -->
        <div class="card shadow-xl">
            <div class="card-header border-b border-slate-200 dark:border-navy-500 px-6 py-4">
                <h3 class="text-xl font-semibold text-slate-800 dark:text-navy-50">Historique des transactions de formation</h3>
            </div>
            
            <div class="card-body p-0">
                @if($ressources->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16">
                        <div class="h-20 w-20 bg-slate-100 dark:bg-navy-600 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-800 dark:text-navy-50 mb-3">Aucune transaction</h3>
                        <p class="text-slate-600 dark:text-navy-200 text-center max-w-md text-lg">Les transactions pour les inscriptions aux formations apparaîtront ici une fois que des apprenants s'inscriront.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-50 dark:bg-navy-800">
                                <tr>
                                    <th class="px-8 py-4 text-left text-xs font-medium text-slate-500 dark:text-navy-200 uppercase tracking-wider">Formation</th>
                                    <th class="px-8 py-4 text-left text-xs font-medium text-slate-500 dark:text-navy-200 uppercase tracking-wider">Transaction</th>
                                    <th class="px-8 py-4 text-left text-xs font-medium text-slate-500 dark:text-navy-200 uppercase tracking-wider">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-navy-500">
                                @foreach($ressources as $res)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-navy-700 transition-colors">
                                        <td class="px-8 py-5">
                                            <div class="flex items-start">
                                                <div class="h-10 w-10 bg-12CEB7/10 rounded-xl flex items-center justify-center mr-4 flex-shrink-0">
                                                    <svg class="w-5 h-5 text-12CEB7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                    </svg>
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <div class="text-sm font-semibold text-slate-900 dark:text-navy-50 break-words">{{ $res->formation->titre ?? 'N/A' }}</div>
                                                    <div class="text-xs text-slate-500 dark:text-navy-200">{{ $res->formation->formationtype->titre ?? '' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 whitespace-nowrap">
                                            <div class="space-y-2">
                                                <!-- Référence -->
                                                <div class="flex items-center">
                                                    <div class="h-6 w-6 bg-slate-100 dark:bg-slate-700 rounded-lg flex items-center justify-center mr-2">
                                                        <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium text-slate-900 dark:text-navy-50">{{ $res->reference }}</div>
                                                        <div class="text-xs text-slate-500 dark:text-navy-200">ID: {{ $res->id }}</div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Type Compte -->
                                                <div class="flex items-center">
                                                    <div class="h-6 w-6 bg-slate-100 dark:bg-slate-700 rounded-lg flex items-center justify-center mr-2">
                                                        <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                                        </svg>
                                                    </div>
                                                    <div>
                                                        <div class="text-sm font-medium text-slate-900 dark:text-navy-50">{{ $res->ressourcecompte->ressourcetype->titre ?? 'N/A' }}</div>
                                                        <div class="text-xs text-slate-500 dark:text-navy-200">{{ $res->ressourcecompte->entreprise->nom ?? '' }}</div>
                                                    </div>
                                                </div>
                                                
                                                <!-- Montant et Date -->
                                                <div class="flex items-center justify-between">
                                                    <div class="flex items-center">
                                                        <div class="h-6 w-6 bg-slate-100 dark:bg-slate-700 rounded-lg flex items-center justify-center mr-2">
                                                            <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                            </svg>
                                                        </div>
                                                        <div>
                                                            @if($res->montant > 0)
                                                                <div class="text-sm font-bold text-green-600 dark:text-green-400">
                                                                    {{ number_format($res->montant, 0, ',', ' ') }} FCFA
                                                                </div>
                                                            @else
                                                                <div class="text-sm font-bold text-amber-600 dark:text-amber-400">
                                                                    Gratuit
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="text-xs text-slate-500 dark:text-navy-200">
                                                        {{ $res->created_at->format('d/m/Y H:i') }}
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 whitespace-nowrap">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                                {{ $res->paiementstatut->titre ?? 'N/A' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <!-- Pagination -->
        @if(method_exists($ressources, 'hasPages') && $ressources->hasPages())
            <div class="mt-6 flex items-center justify-between">
                <div class="text-sm text-slate-700 dark:text-navy-200">
                    Affichage de {{ $ressources->firstItem() }} à {{ $ressources->lastItem() }} sur {{ $ressources->total() }} résultats
                </div>
                {{ $ressources->links() }}
            </div>
        @endif
    </main>

    <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
        @include('layouts.sidebar')
    </div>
</x-app-layout>
