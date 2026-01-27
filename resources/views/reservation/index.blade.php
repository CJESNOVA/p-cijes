<x-app-layout title="Mes Réservations" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne avec identité unique -->
        <div class="mb-10">
            <div class="flex items-center gap-4 mb-8">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">Mes Réservations</h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">Gestion de mes réservations d'espaces</p>
                </div>
            </div>

            <!-- Cartes statistiques avec design unique -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="card bg-gradient-to-br from-teal-500 to-cyan-600 text-white border-0 shadow-xl">
                    <div class="card-body p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-teal-100 text-sm font-medium">Total Réservations</p>
                                <p class="text-3xl font-bold mt-2">{{ $reservations ? $reservations->count() : 0 }}</p>
                            </div>
                            <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-gradient-to-br from-emerald-500 to-green-600 text-white border-0 shadow-xl">
                    <div class="card-body p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-emerald-100 text-sm font-medium">Réservations Actives</p>
                                <p class="text-3xl font-bold mt-2">{{ $reservations ? $reservations->where('reservationstatut_id', 1)->count() : 0 }}</p>
                            </div>
                            <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card bg-gradient-to-br from-sky-500 to-blue-600 text-white border-0 shadow-xl">
                    <div class="card-body p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sky-100 text-sm font-medium">Espaces Uniques</p>
                                <p class="text-3xl font-bold mt-2">{{ $reservations ? $reservations->pluck('espace_id')->unique()->count() : 0 }}</p>
                            </div>
                            <div class="h-12 w-12 bg-white/20 rounded-xl flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages -->
        @if(session('success'))
            <div class="alert flex rounded-lg bg-green-500 px-6 py-4 text-white mb-6 shadow-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Tableau moderne des réservations -->
        <div class="card shadow-xl">
            <div class="card-header border-b border-slate-200 dark:border-navy-500 px-6 py-4">
                <h3 class="text-xl font-semibold text-slate-800 dark:text-navy-50">Historique de mes réservations</h3>
            </div>
            
            <div class="card-body p-0">
                @if(!$reservations || $reservations->isEmpty())
                    <div class="flex flex-col items-center justify-center py-16">
                        <div class="h-20 w-20 bg-slate-100 dark:bg-navy-600 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-10 h-10 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-slate-800 dark:text-navy-50 mb-3">Aucune réservation</h3>
                        <p class="text-slate-600 dark:text-navy-200 text-center max-w-md text-lg">Vous n'avez pas encore de réservation. Réservez un espace pour voir vos réservations apparaître ici.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-slate-50 dark:bg-navy-800">
                                <tr>
                                    <th class="px-8 py-4 text-left text-xs font-medium text-slate-500 dark:text-navy-200 uppercase tracking-wider">Espace</th>
                                    <th class="px-8 py-4 text-left text-xs font-medium text-slate-500 dark:text-navy-200 uppercase tracking-wider">Période</th>
                                    <th class="px-8 py-4 text-left text-xs font-medium text-slate-500 dark:text-navy-200 uppercase tracking-wider">Statut</th>
                                    <th class="px-8 py-4 text-left text-xs font-medium text-slate-500 dark:text-navy-200 uppercase tracking-wider">Observation</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-navy-500">
                                @foreach($reservations as $r)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-navy-700 transition-colors">
                                        <td class="px-8 py-5 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if($r->espace && $r->espace->vignette)
                                                    <img class="h-12 w-12 rounded-lg object-cover object-center mr-4"
                                                        src="{{ env('SUPABASE_BUCKET_URL') . '/' . $r->espace->vignette }}" 
                                                        alt="{{ $r->espace->titre }}" />
                                                @else
                                                    <div class="h-12 w-12 bg-teal-100 dark:bg-teal-900/30 rounded-lg flex items-center justify-center mr-4">
                                                        <svg class="w-6 h-6 text-teal-600 dark:text-teal-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                        </svg>
                                                    </div>
                                                @endif
                                                <div>
                                                    <div class="text-sm font-semibold text-slate-900 dark:text-navy-50">{{ $r->espace->titre ?? 'Espace non défini' }}</div>
                                                    <div class="text-xs text-slate-500 dark:text-navy-200">{{ $r->espace->espacetype->titre ?? '' }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 whitespace-nowrap">
                                            <div class="text-sm font-medium text-slate-900 dark:text-navy-50">
                                                {{ \Carbon\Carbon::parse($r->datedebut)->format('d/m/Y') }}
                                            </div>
                                            <div class="text-xs text-slate-500 dark:text-navy-200">
                                                → {{ \Carbon\Carbon::parse($r->datefin)->format('d/m/Y') }}
                                            </div>
                                        </td>
                                        <td class="px-8 py-5 whitespace-nowrap">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                                @if($r->reservationstatut_id == 1) bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400
                                                @elseif($r->reservationstatut_id == 2) bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-400
                                                @else bg-slate-100 text-slate-800 dark:bg-slate-900/30 dark:text-slate-400
                                                @endif">
                                                {{ $r->reservationstatut->titre ?? 'Non défini' }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-5">
                                            <div class="text-sm text-slate-600 dark:text-navy-200 max-w-xs truncate">
                                                {{ $r->observation ?? 'Aucune observation' }}
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </main>

    <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
        @include('layouts.sidebar')
    </div>
</x-app-layout>
