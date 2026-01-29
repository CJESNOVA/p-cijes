{{-- resources/views/dashboard/partials/reservations.blade.php --}}
<div class="mt-4 ml-4 grid grid-cols-12 gap-4" style="margin-left: 30px;">
    <div class="col-span-12 card p-6 space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-700">Réservations</h3>
        </div>

        <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @forelse($reservations as $res)
                @php
                    $statusColor = match($res->reservationstatut->titre ?? 'En attente') {
                        'Confirmée' => 'bg-[#12CEB7]/10 text-[#12CEB7]',
                        'Annulée' => 'bg-[#9333EA]/10 text-[#9333EA]',
                        'En attente' => 'bg-[#F09116]/10 text-[#F09116]',
                        default => 'bg-gray-100 text-gray-800',
                    };
                @endphp

                <div class="flex flex-col bg-white rounded-xl shadow hover:shadow-lg transition-shadow duration-300 p-5 space-y-3">
                    <div>
                        <p class="font-medium text-slate-800">{{ $res->espace->titre ?? 'Consultation' }}</p>
                        @php
                            $dateDebut = \Carbon\Carbon::parse($res->datedebut);
                            $dateFin = \Carbon\Carbon::parse($res->datefin);
                        @endphp

                        <p class="text-xs text-slate-400">
                            @if($dateDebut->isSameDay($dateFin))
                                {{ $dateDebut->format('d M Y') }}
                            @else
                                {{ $dateDebut->format('d M Y') }} 
                                — {{ $dateFin->format('d M Y') }}
                            @endif
                        </p>
                    </div>

                    <div class="flex justify-between items-center mt-2">
                        <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $statusColor }}">
                            {{ $res->reservationstatut->titre ?? 'En attente' }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="col-span-1 p-4 bg-yellow-50 rounded-lg text-yellow-800 font-medium text-center">
                    Aucune réservation pour le moment.
                </div>
            @endforelse
        </div>
    </div>
</div>
