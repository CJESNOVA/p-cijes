<!-- resources/views/sidebar.blade.php -->
@php
    use App\Models\Evenement;
    use App\Models\Espace;
    use App\Models\Prestation;
    use App\Models\Formation;

    $evenements  = Evenement::where('etat', 1)->where('dateevenement', '>=', now())->orderBy('dateevenement', 'asc')->take(5)->get();
    $espaces     = Espace::where('etat', 1)->get();
    $prestations = Prestation::where('etat', 1)->get();
    $formations  = Formation::where('etat', 1)->get();
@endphp

<div class="card">
    <div class="px-4 pt-2 pb-5 sm:px-5">

        {{-- Événements à venir --}}
        <div class="mt-5">
            <p class="border-b border-slate-200 pb-2 text-base text-slate-800 dark:border-navy-600 dark:text-navy-100">
                📅 Événements à venir
            </p>
            <ul class="mt-3 space-y-2">
                @forelse($evenements as $event)
                    <li class="flex justify-between">
                        <span>{{ $event->titre }}</span>
                        <span class="text-xs text-slate-400">{{ \Carbon\Carbon::parse($event->dateevenement)->format('d/m/Y') }}</span>
                    </li>
                @empty
                    <li class="text-slate-400">Aucun événement</li>
                @endforelse
            </ul>
        </div>

        {{-- Espaces physiques --}}
        <div class="mt-5">
            <p class="border-b pb-2 text-base text-slate-800 dark:text-navy-100">
                🏢 Espaces physiques
            </p>
            <ul class="mt-3 space-y-2">
                @forelse($espaces as $espace)
                    <li>{{ $espace->titre }}</li>
                @empty
                    <li class="text-slate-400">Aucun espace</li>
                @endforelse
            </ul>
        </div>

        {{-- Prestations --}}
        <div class="mt-5">
            <p class="border-b pb-2 text-base text-slate-800 dark:text-navy-100">
                💼 Prestations
            </p>
            <ul class="mt-3 space-y-2">
                @forelse($prestations as $prestation)
                    <li>{{ $prestation->titre }}</li>
                @empty
                    <li class="text-slate-400">Aucune prestation</li>
                @endforelse
            </ul>
        </div>

        {{-- Formations ouvertes --}}
        <div class="mt-5">
            <p class="border-b pb-2 text-base text-slate-800 dark:text-navy-100">
                🎓 Formations ouvertes
            </p>
            <ul class="mt-3 space-y-2">
                @forelse($formations as $formation)
                    <li>{{ $formation->titre }}</li>
                @empty
                    <li class="text-slate-400">Aucune formation</li>
                @endforelse
            </ul>
        </div>

    </div>
</div>
