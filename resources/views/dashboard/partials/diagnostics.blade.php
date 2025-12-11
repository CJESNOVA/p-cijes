{{-- resources/views/dashboard/partials/diagnostics.blade.php --}}
<div class="mt-4 ml-4 grid grid-cols-12 gap-4" style="margin-left: 30px;">
    <div class="col-span-12 card p-6 space-y-6">
    <div class="flex items-center justify-between">
        <h3 class="text-lg font-semibold text-slate-700">Diagnostics réalisés</h3>
    </div>

    @if($diagnostics->isEmpty())
        <div class="p-4 bg-yellow-50 rounded-lg text-yellow-800 text-center font-medium">
            Aucun diagnostic réalisé pour le moment.
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($diagnostics as $diag)
                <div class="flex flex-col bg-white rounded-xl shadow hover:shadow-lg transition-shadow duration-300 p-5 space-y-3">
                    <div class="flex-1">
                        @if(!empty($diag->entreprise))
                            <p class="font-medium text-slate-800">
                                {{ $diag->entreprise->nom }}
                            </p>
                        @else
                            <p class="font-medium text-slate-800">
                                {{ $diag->membre->prenom ?? '-' }} {{ $diag->membre->nom ?? '-' }}
                            </p>
                        @endif
                        {{-- <p class="text-sm text-slate-600">
                            Accompagnement : {{ $diag->accompagnement->entreprise->nom ?? '-' }}
                        </p> --}}
                    </div>
                    <div class="flex items-center space-x-4 mt-2 sm:mt-0">
                        <span class="text-sm font-medium text-green-600">
                            Score : {{ $diag->scoreglobal ?? '—' }}%
                        </span>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
</div>
