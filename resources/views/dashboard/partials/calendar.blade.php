{{-- resources/views/dashboard/partials/calendar.blade.php --}}
<div class="card p-4" style="margin-left: 30px; margin-right: 30px;">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-slate-800 dark:text-navy-100">Agenda</h3>
        <a href="{{ route('evenement.index') }}" class="text-xs text-primary hover:underline">Voir tout</a>
    </div>

    <div class="mt-3">
        {{-- Mini calendrier interactif --}}
        {{-- <div id="mini-calendar" class="h-64 w-full rounded-xl shadow-sm"></div> --}}

        {{-- Liste des événements à venir --}}
<ul class="mt-4 space-y-3">
    @forelse($calendarEvents as $event)
        @php
            $inscrit = $event->inscriptions->contains(fn($insc) => $insc->membre_id === $membre->id);
        @endphp
        <li class="flex items-center space-x-3 rounded-lg p-3 transition-shadow duration-200 hover:shadow-md
                   {{ $inscrit ? 'bg-green-50 dark:bg-green-700/20' : 'bg-slate-100 dark:bg-navy-600' }}">
            {{-- Vignette de l’événement --}}
            <div class="flex-shrink-0">
                @if(!empty($event->vignette))
                    <img src="{{ env('SUPABASE_BUCKET_URL') . '/' . '' . $event->vignette }}" 
                         alt="{{ $event->titre }}" 
                         class="w-16 h-16 object-cover rounded-md border border-slate-200 dark:border-navy-500">
                @else
                    <div class="w-16 h-16 flex items-center justify-center rounded-md bg-slate-200 dark:bg-navy-500 text-slate-600 dark:text-navy-100 text-xs">
                        Aucune<br>image
                    </div>
                @endif
            </div>

            {{-- Infos sur l’événement --}}
            <div class="flex-1">
                <p class="font-semibold text-slate-700 dark:text-navy-100">
                    {{ $event->titre }}
                </p>
                <p class="text-xs text-slate-400 dark:text-navy-300">
                    {{ \Carbon\Carbon::parse($event->dateevenement)->translatedFormat('d M Y à H:i') }}
                </p>

                @if($inscrit)
                    <span class="inline-flex items-center mt-1 text-xs text-green-700 bg-green-100 dark:bg-green-600/40 dark:text-green-200 px-2 py-0.5 rounded-full">
                        <i class="fas fa-check-circle mr-1"></i> Vous êtes inscrit
                    </span>
                @else
                    <span class="inline-flex items-center mt-1 text-xs text-slate-600 bg-slate-200 dark:bg-navy-700 dark:text-navy-200 px-2 py-0.5 rounded-full">
                        <i class="fas fa-calendar-alt mr-1"></i> Non inscrit
                    </span>
                @endif
            </div>

            {{-- Bouton d’action --}}
            <div>
                <a href="{{ route('evenement.show', $event->id) }}" 
                   class="text-primary text-xs font-medium hover:underline">
                    Détails
                </a>
            </div>
        </li>
    @empty
        <li class="p-3 bg-yellow-50 text-sm text-slate-600 rounded dark:bg-navy-700/40 dark:text-navy-200">
            Aucun événement à venir
        </li>
    @endforelse
</ul>

    </div>

{{-- Liste des espaces physiques disponibles --}}
<div class="mt-6">
    <h3 class="text-base font-semibold text-slate-700 dark:text-navy-100 mb-3">
        Espaces physiques disponibles
    </h3>

    <ul class="space-y-2">
        @forelse($espaces as $espace)
            <li class="flex items-center justify-between rounded-lg p-3 bg-slate-100 dark:bg-navy-600 hover:bg-slate-200 dark:hover:bg-navy-500 transition">
                <div class="flex items-center space-x-3">
                    {{-- Vignette de l’espace --}}
                    @if($espace->vignette)
                        <img src="{{ env('SUPABASE_BUCKET_URL') . '/' . '' . $espace->vignette }}"
                             alt="{{ $espace->titre }}"
                             class="w-14 h-14 rounded-lg object-cover shadow-sm">
                    @else
                        <div class="w-14 h-14 flex items-center justify-center bg-slate-200 dark:bg-navy-700 rounded-lg text-slate-400">
                            <i class="fa-solid fa-building text-lg"></i>
                        </div>
                    @endif

                    {{-- Détails --}}
                    <div>
                        <p class="font-medium text-slate-700 dark:text-navy-100">{{ $espace->titre }}</p>
                        <p class="text-xs text-slate-500 dark:text-navy-300">
                            {{ $espace->espacetype->titre ?? 'Non spécifié' }}
                            @if($espace->capacite)
                                <br>• Capacité : {{ $espace->capacite }}
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Bouton Réserver / Détails --}}
                <div>
                    <a href="{{ route('espace.show', $espace->id) }}"
                       class="px-3 py-1.5 text-xs font-medium rounded-lg bg-primary/10 text-primary hover:bg-primary hover:text-white transition">
                        Détails
                    </a>
                </div>
            </li>
        @empty
            <li class="p-3 bg-yellow-50 text-sm text-slate-600 rounded dark:bg-navy-700/40 dark:text-navy-200">
                Aucun espace disponible
            </li>
        @endforelse
    </ul>
</div>

</div>

@push('scripts')
    {{-- FullCalendar (CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const calendarEl = document.getElementById('mini-calendar');

            if (calendarEl) {
                const calendar = new FullCalendar.Calendar(calendarEl, {
                    initialView: 'dayGridMonth',
                    height: '100%',
                    headerToolbar: false, // pas de barre de navigation
                    aspectRatio: 1.4,
                    contentHeight: 'auto',
                    events: [
                        @foreach($calendarEvents as $event)
                            {
                                title: "{{ Str::limit($event->titre, 20) }}",
                                start: "{{ $event->dateevenement }}",
                                backgroundColor: "{{ $event->inscriptions->contains(fn($insc) => $insc->membre_id === $membre->id) ? '#22c55e' : '#3b82f6' }}",
                                borderColor: "{{ $event->inscriptions->contains(fn($insc) => $insc->membre_id === $membre->id) ? '#16a34a' : '#2563eb' }}",
                                textColor: 'white',
                                url: "{{ route('evenement.show', $event->id) }}"
                            },
                        @endforeach
                    ],
                });

                calendar.render();
            }
        });
    </script>
@endpush
