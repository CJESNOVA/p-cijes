{{-- resources/views/dashboard/partials/entreprises.blade.php --}}
<div class="mt-4 ml-4 grid grid-cols-12 gap-4" style="margin-left: 30px; margin-right: 30px;">
    <div class="col-span-12 card p-6 space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-700">Mes entreprises</h3>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-1 lg:grid-cols-1 gap-6">
            @forelse($entreprises as $entreprise)
                <div class="flex flex-col bg-white rounded-xl shadow hover:shadow-lg transition-shadow duration-300 p-5 space-y-3">
                    <div class="flex items-center space-x-4">
                        @if($entreprise->vignette)
                            <img src="{{ env('SUPABASE_BUCKET_URL') . '/' . $entreprise->vignette }}" 
                                 alt="{{ $entreprise->nom }}" 
                                 class="w-14 h-14 rounded-full object-cover">
                        @else
                            <div class="w-14 h-14 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-lg">
                                {{ strtoupper(substr($entreprise->nom, 0, 2)) }}
                            </div>
                        @endif
                        <div class="flex-1">
                            <p class="font-medium text-slate-800 text-sm sm:text-base">{{ $entreprise->nom }}</p>
                            <p class="text-xs text-slate-400">{{ $entreprise->adresse ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-1 p-6 bg-yellow-50 rounded-lg text-yellow-800 font-medium text-center">
                    Vous n'avez pas encore d'entreprise enregistrée.
                </div>
            @endforelse
        </div>
    </div>
</div>
