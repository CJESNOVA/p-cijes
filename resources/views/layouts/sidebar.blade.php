<!-- resources/views/layouts/sidebar.blade.php -->
@php
    use App\Models\Evenement;
    use App\Models\Espace;
    use App\Models\Proposition;

    $userId = auth()->id();
    $membre = \App\Models\Membre::where('user_id', $userId)->first();
    
    $evenements  = Evenement::where('etat', 1)->orderBy('dateevenement', 'asc')->take(3)->get();
    $espaces     = Espace::where('etat', 1)->where('pays_id', $membre->pays_id)->take(2)->get();
    
    // Récupérer le nombre de propositions reçues
    $propositionsRecuesCount = 0;
    if ($membre) {
        $plansIds = \App\Models\Plan::whereHas('accompagnement', function($query) use ($membre) {
            $query->where('membre_id', $membre->id);
        })->pluck('id');
        
        $propositionsRecuesCount = Proposition::whereIn('plan_id', $plansIds)
            ->whereHas('statut', function($query) {
                $query->where('titre', 'En attente');
            })
            ->count();
    }
@endphp

<div class="space-y-6">
    <!-- Événements à venir -->
    @if($evenements->isNotEmpty())
    <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 dark:from-indigo-900/20 dark:to-indigo-800/20 rounded-xl border border-indigo-200 dark:border-indigo-700 overflow-hidden">
        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 px-4 py-3">
            <h3 class="text-white font-semibold flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                Événements à venir
            </h3>
        </div>
        <div class="p-4 space-y-3">
            @foreach($evenements as $evenement)
            <div class="bg-white dark:bg-navy-800 rounded-lg p-3 border border-indigo-100 dark:border-indigo-800 hover:shadow-md transition-shadow">
                <div class="flex items-start space-x-3">
                    @if($evenement && $evenement->vignette)
                    <div class="flex-shrink-0">
                        <img class="w-12 h-12 rounded-lg object-cover" 
                             src="{{ env('SUPABASE_BUCKET_URL') . '/' . $evenement->vignette }}" 
                             alt="{{ $evenement->titre }}" />
                    </div>
                    @else
                    <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-indigo-100 dark:bg-indigo-800 flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-slate-800 dark:text-navy-100 truncate">
                            <a href="{{ route('evenement.show', $evenement->id) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                {{ $evenement->titre }}
                            </a>
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-navy-400 mt-1">
                            {{ \Carbon\Carbon::parse($evenement->dateevenement)->format('d M Y') }}
                        </p>
                        <p class="text-xs text-indigo-600 dark:text-indigo-400 font-medium mt-1">
                            {{ $evenement->evenementtype->titre ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Espaces disponibles -->
    @if($espaces->isNotEmpty())
    <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 dark:from-emerald-900/20 dark:to-emerald-800/20 rounded-xl border border-emerald-200 dark:border-emerald-700 overflow-hidden">
        <div class="bg-gradient-to-r from-emerald-500 to-emerald-600 px-4 py-3">
            <h3 class="text-white font-semibold flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                Espaces disponibles
            </h3>
        </div>
        <div class="p-4 space-y-3">
            @foreach($espaces as $espace)
            <div class="bg-white dark:bg-navy-800 rounded-lg p-3 border border-emerald-100 dark:border-emerald-800 hover:shadow-md transition-shadow">
                <div class="flex items-start space-x-3">
                    @if($espace && $espace->vignette)
                    <div class="flex-shrink-0">
                        <img class="w-12 h-12 rounded-lg object-cover" 
                             src="{{ env('SUPABASE_BUCKET_URL') . '/' . $espace->vignette }}" 
                             alt="{{ $espace->titre }}" />
                    </div>
                    @else
                    <div class="flex-shrink-0 w-12 h-12 rounded-lg bg-emerald-100 dark:bg-emerald-800 flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <h4 class="text-sm font-semibold text-slate-800 dark:text-navy-100 truncate">
                            <a href="{{ route('espace.show', $espace->id) }}" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">
                                {{ $espace->titre }}
                            </a>
                        </h4>
                        <p class="text-xs text-slate-500 dark:text-navy-400 mt-1 line-clamp-2">
                            {{ Str::limit($espace->resume ?? '', 80) }}
                        </p>
                        <p class="text-xs text-emerald-600 dark:text-emerald-400 font-medium mt-1">
                            {{ $espace->espacetype->titre ?? '-' }}
                        </p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Propositions reçues -->
    @if($membre && $propositionsRecuesCount > 0)
    <div class="bg-gradient-to-br from-amber-50 to-amber-100 dark:from-amber-900/20 dark:to-amber-800/20 rounded-xl border border-amber-200 dark:border-amber-700 overflow-hidden">
        <div class="bg-gradient-to-r from-amber-500 to-amber-600 px-4 py-3">
            <h3 class="text-white font-semibold flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Propositions reçues
            </h3>
        </div>
        <div class="p-4">
            <div class="bg-white dark:bg-navy-800 rounded-lg p-4 border border-amber-100 dark:border-amber-800">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-amber-100 dark:bg-amber-800 flex items-center justify-center">
                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-amber-800 dark:text-amber-200">
                                En attente de réponse
                            </p>
                            <p class="text-xs text-amber-600 dark:text-amber-400">
                                {{ $propositionsRecuesCount }} proposition{{ $propositionsRecuesCount > 1 ? 's' : '' }} à traiter
                            </p>
                        </div>
                    </div>
                    <a href="{{ route('proposition.membre.index') }}" 
                       class="inline-flex items-center px-3 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg transition-colors">
                        Voir
                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Section vide si aucun contenu -->
    @if($evenements->isEmpty() && $espaces->isEmpty() && (!$membre || $propositionsRecuesCount == 0))
    <div class="bg-gradient-to-br from-slate-50 to-slate-100 dark:from-navy-900/20 dark:to-navy-800/20 rounded-xl border border-slate-200 dark:border-navy-700 p-6 text-center">
        <svg class="w-12 h-12 text-slate-400 dark:text-navy-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
        </svg>
        <p class="text-sm text-slate-600 dark:text-navy-400">
            Aucun contenu disponible pour le moment
        </p>
    </div>
    @endif
</div>
