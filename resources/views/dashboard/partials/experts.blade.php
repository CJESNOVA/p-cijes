{{-- resources/views/dashboard/partials/experts.blade.php --}}
<div class="mt-4 ml-4 grid grid-cols-12 gap-4" style="margin-left: 30px;">
    <div class="col-span-12 card p-6 space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-slate-700">Experts & Conseillers</h3>
        </div>

        <div class="space-y-6">
            {{-- Experts disponibles --}}
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <p class="text-sm font-medium text-slate-600 mb-2">Experts disponibles dans votre pays</p>
                    <a href="{{ route('expert.liste') }}" class="btn btn-outline btn-sm">Voir tous</a>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    @forelse($experts as $exp)
                        <div class="flex flex-col bg-white rounded-xl shadow hover:shadow-lg transition-shadow duration-300 p-5 space-y-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-600 font-bold text-lg">
                                    @if($exp->membre && $exp->membre->vignette)
                                    <img class="rounded-full " src="{{ env('SUPABASE_BUCKET_URL') . '/' . $exp->membre->vignette }}" alt="avatar" />
                                @endif
                                </div>
                                <div class="flex-1">
                                    <p class="font-medium text-slate-800">{{ $exp->domaine }}</p>
                                    <p class="text-xs text-slate-400">
                                        {{ $exp->membre->prenom ?? '-' }} {{ $exp->membre->nom ?? '-' }} • {{ $exp->experttype->titre ?? '-' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex justify-end space-x-3 mt-2">
                                @unless($exp->membre_id === optional($membre)->id)
                                    <a href="{{ route('evaluation.create', $exp->id) }}" class="text-green-600 text-xs font-medium hover:underline">Evaluer</a>
                                @endunless
                                <a href="{{ route('expert.show', $exp) }}" class="text-blue-600 text-xs font-medium hover:underline">Voir</a>
                            </div>
                        </div>
                    @empty
                        <div class="p-4 bg-yellow-50 rounded-lg text-yellow-800 text-center font-medium">
                            Pas d'experts trouvés.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Conseillers (profils du membre) --}}
            @if(!empty($conseillers) && $conseillers->count())
    <div class="space-y-3">
        <div class="flex items-center justify-between">
            <p class="text-sm font-medium text-slate-600">Mes conseillers</p>
            <a href="{{ route('conseiller.mes_conseillers') }}" class="btn btn-outline btn-sm">Voir tous</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($conseillers as $c)
                <div class="flex flex-col bg-white rounded-xl shadow hover:shadow-lg transition-shadow duration-300 p-5 space-y-3">
                    <div class="flex items-center space-x-3">
                        {{-- Initiales ou avatar --}}
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600 font-bold text-lg">
                            @if($c->conseiller->membre && $c->conseiller->membre->vignette)
                                    <img class="rounded-full " src="{{ env('SUPABASE_BUCKET_URL') . '/' . $c->conseiller->membre->vignette }}" alt="avatar" />
                                @endif
                        </div>

                        <div class="flex-1">
                            <p class="font-medium text-slate-800">{!! $c->conseiller->fonction ?? '—' !!}</p>
                            <p class="text-xs text-slate-400">{{ $c->conseiller->conseillertype->titre ?? '—' }}</p>
                            @if(!empty($c->accompagnement))
                                <p class="text-xs text-slate-400 mt-1">
                                    Accompagnement :
                                    {{ $c->accompagnement->entreprise->nom ?? '—' }} •
                                    {{ $c->accompagnement->membre->prenom ?? '' }} {{ $c->accompagnement->membre->nom ?? '' }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Prescriptions --}}
                    @if($c->conseiller->prescriptions->isNotEmpty())
                        <div class="mt-2 border-t pt-2">
                            <h4 class="text-sm font-semibold text-slate-700">Prescriptions :</h4>
                            <ul class="list-disc pl-5 text-xs text-slate-500">
                                @foreach($c->conseiller->prescriptions as $p)
                                    <li class="mt-1">
                                        @if($p->prestation)
                                            Prestation : {{ $p->prestation->titre }} 
                                            ({{ $p->prestation->prix }} FCFA, {{ $p->prestation->duree }})
                                        @endif
                                        @if($p->formation)
                                            Formation : {{ $p->formation->titre }} 
                                            ({{ $p->formation->formationniveau->titre ?? 'Niveau N/A' }})
                                        @endif
                                        <span class="text-xs text-slate-400">[{{ $p->created_at->format('d/m/Y') }}]</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @else
                        <p class="text-xs text-slate-400 mt-2">Aucune prescription.</p>
                    @endif

                </div>
            @endforeach
        </div>
    </div>
@endif


        </div>
    </div>
</div>
