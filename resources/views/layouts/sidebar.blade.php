<!-- resources/views/sidebar.blade.php -->
@php
    use App\Models\Evenement;
    use App\Models\Espace;
    use App\Models\Prestation;
    use App\Models\Formation;
    use App\Models\Proposition;

    $userId = auth()->id();
    $membre = \App\Models\Membre::where('user_id', $userId)->first();
    
    $evenements  = Evenement::where('etat', 1)->orderBy('dateevenement', 'asc')->take(5)->limit(1)->get();
    $espaces     = Espace::where('etat', 1)->limit(1)->get();
    $prestations = Prestation::where('etat', 1)->limit(2)->get();
    $formations  = Formation::where('etat', 1)->limit(2)->get();
    
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

<div>
{{-- Espaces physiques --}}
@if($espaces)
<div class="card">
    @foreach($espaces as $espace)
    @if($espace && $espace->vignette)
    <div class="h-24 rounded-t-lg bg-primary dark:bg-accent">
        <img class="h-full w-full rounded-t-lg object-cover object-center"
            src="{{ env('SUPABASE_BUCKET_URL') . '/' . $espace->vignette }}" alt="{{ $espace->titre }}" />
    </div>
    @endif
    <div class="px-4 pt-2 pb-5 sm:px-5">
        <!--<div class="avatar -mt-12 size-20">
            <img class="rounded-full border-2 border-white dark:border-navy-700"
                src="{{ asset('images/200x200.png') }}" alt="avatar" />
        </div>-->
        <h3 class="pt-2 text-lg font-medium text-slate-700 dark:text-navy-100">
            <a href="{{ route('espace.show', $espace->id) }}">{{ $espace->titre }}</a>
        </h3>
        <p class="text-xs-plus text-slate-400 dark:text-navy-300">
            {{ $espace->espacetype->titre ?? '-' }}
        </p>
        <p class="mt-3">
            {{ $espace->resume }}
        </p>
    </div>
    @endforeach
</div>
@endif



@if($formations)
{{-- Formations --}}
<div class="mt-5">
    <p
        class="border-b border-slate-200 pb-2 text-base text-slate-800 dark:border-navy-600 dark:text-navy-100">
            💼 Formations
    </p>
    <div class="mt-3 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-1">
                @foreach($formations as $formation)
        <div class="flex justify-between space-x-2">
            <div class="flex flex-1 flex-col justify-between">
                <div>
                    <div class="mt-1 text-slate-800 line-clamp-3 dark:text-navy-100">
                        <a href="#"
                            class="font-medium text-slate-700 hover:text-primary focus:text-primary dark:text-navy-100 dark:hover:text-accent-light dark:focus:text-accent-light">{{ $formation->titre }}</a>
                    </div>
                    <p class="text-xs font-medium line-clamp-1">Du {{ $formation->datedebut }} au {{ $formation->datefin }}</p>
                </div>
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium line-clamp-1">{{ $formation->formationniveau->titre ?? '' }}</p>

                    </div>
                </div>
            </div>
        </div>
                @endforeach
</div>
<br />
@endif



{{-- Evenements --}}
@if($evenements)
<div class="card">
    @foreach($evenements as $evenement)
    @if($evenement && $evenement->vignette)
    <div class="h-24 rounded-t-lg bg-primary dark:bg-accent">
        <img class="h-full w-full rounded-t-lg object-cover object-center"
            src="{{ env('SUPABASE_BUCKET_URL') . '/' . $evenement->vignette }}" alt="{{ $evenement->titre }}" />
    </div>
    @endif
    <div class="px-4 pt-2 pb-5 sm:px-5">
        <!--<div class="avatar -mt-12 size-20">
            <img class="rounded-full border-2 border-white dark:border-navy-700"
                src="{{ asset('images/200x200.png') }}" alt="avatar" />
        </div>-->
        <h3 class="pt-2 text-lg font-medium text-slate-700 dark:text-navy-100">
            <a href="{{ route('evenement.show', $evenement->id) }}">{{ $evenement->titre }}</a>
        </h3>
        <p class="text-xs-plus text-slate-400 dark:text-navy-300">
            {{ $evenement->evenementtype->titre ?? '-' }}
        </p>
        <p class="mt-3">
            {{ \Carbon\Carbon::parse($evenement->dateevenement)->format('d F Y') }}
        </p>
    </div>
    @endforeach
</div>
@endif



@if($prestations)
{{-- Prestations --}}
<div class="mt-5">
    <p
        class="border-b border-slate-200 pb-2 text-base text-slate-800 dark:border-navy-600 dark:text-navy-100">
            💼 Prestations
    </p>
    <div class="mt-3 grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-1">
                @foreach($prestations as $prestation)
        <div class="flex justify-between space-x-2">
            <div class="flex flex-1 flex-col justify-between">
                <div>
                    <p class="text-xs font-medium line-clamp-1">{{ $prestation->prix }} - {{ $prestation->duree }}</p>
                    <div class="mt-1 text-slate-800 line-clamp-3 dark:text-navy-100">
                        <a href="#"
                            class="font-medium text-slate-700 hover:text-primary focus:text-primary dark:text-navy-100 dark:hover:text-accent-light dark:focus:text-accent-light">{{ $prestation->titre }}</a>
                    </div>
                </div>
                <div class="flex items-center justify-between">
                    <p class="text-xs font-medium line-clamp-1">{{ $prestation->prestationtype->titre ?? '' }}</p>

                    </div>
                </div>
            </div>
        </div>
                @endforeach
    </div>
@endif
</div>

{{-- Propositions reçues --}}
@if($membre && $propositionsRecuesCount > 0)
<div class="mt-5">
    <p class="border-b border-slate-200 pb-2 text-base text-slate-800 dark:border-navy-600 dark:text-navy-100">
        📋 Propositions reçues
    </p>
    <div class="mt-3">
        <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-lg p-3 border border-yellow-200 dark:border-yellow-800">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-yellow-800 dark:text-yellow-200">En attente de réponse</p>
                    <p class="text-xs text-yellow-600 dark:text-yellow-400">{{ $propositionsRecuesCount }} proposition{{ $propositionsRecuesCount > 1 ? 's' : '' }}</p>
                </div>
                <a href="{{ route('proposition.membre.index') }}" class="btn btn-sm btn-warning">
                    Voir
                </a>
            </div>
        </div>
    </div>
</div>
@endif

</div>

