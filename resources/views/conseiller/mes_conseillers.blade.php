<x-app-layout title="Mes conseillers" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Mes conseillers
          </h2>
          <div class="hidden h-full py-1 sm:flex">
            <div class="h-full w-px bg-slate-300 dark:bg-navy-600"></div>
          </div>
          <!-- <ul class="hidden flex-wrap items-center space-x-2 sm:flex">
            <li class="flex items-center space-x-2">
              <a
                class="text-primary transition-colors hover:text-primary-focus dark:text-accent-light dark:hover:text-accent"
                href="#"
                >Forms</a
              >
              <svg
                x-ignore
                xmlns="http://www.w3.org/2000/svg"
                class="size-4"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M9 5l7 7-7 7"
                />
              </svg>
            </li>
            <li>Mes conseillers</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">


@if($conseillers)
              <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-2 lg:gap-6 xl:grid-cols-2">

        @if($conseillers->isEmpty())
            <p class="text-slate-500">Aucun conseiller lié à vos entreprises.</p>
        @else
                @foreach($conseillers as $c)
                    <div class="card p-4">
                        <h3 class="text-lg font-semibold">
                            {{ $c->conseiller->membre->nom ?? '' }} {{ $c->conseiller->membre->prenom ?? '' }}
                        </h3>
                        <p class="text-sm text-slate-500">
                            Type : {{ $c->conseiller->conseillertype->titre ?? '—' }}
                        </p>
                        <p class="text-sm">Fonction : {!! $c->conseiller->fonction ?? '—' !!}</p>
                        <p class="text-sm text-slate-400">
                            Accompagnement :
                            {{ $c->accompagnement->entreprise->nom ?? '' }} 
                            {{ $c->accompagnement->membre->nom ?? '' }} {{ $c->accompagnement->membre->prenom ?? '' }}
                        </p>

                        {{-- Prescriptions de ce conseiller --}}
                        @if($c->conseiller->prescriptions->isNotEmpty())
                            <div class="mt-3 border-t pt-2">
                                <h4 class="text-sm font-semibold">Prescriptions :</h4>
                                <ul class="list-disc pl-5 text-sm text-slate-600">
                                    @foreach($c->conseiller->prescriptions as $p)
                                        <li>
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

        @endif
          </div>
@endif
          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>