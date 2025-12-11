<x-app-layout title="Ressources utilisées pour les évènements" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Ressources utilisées pour les évènements
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
            <li>Mes prescriptions</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

        @if(session('success'))
            <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">{{ session('success') }}</div>
        @endif

              <div class="grid grid-cols-1 gap-4 sm:grid-cols-1 sm:gap-5 lg:grid-cols-1 lg:gap-6 xl:grid-cols-1">

    @if($ressources->isEmpty())
        <p>Aucune ressource utilisée pour le moment.</p>
    @else
        <div class="card p-4 sm:p-5">
            @foreach($ressources as $res)
                <div>
                    <p><strong>Compte ressource :</strong> {{ $res->ressourcecompte->nom_complet }}</p>
                    <p><strong>Evènement :</strong> {{ $res->evenement->titre ?? 'N/A' }}</p>
                    <p><strong>Montant :</strong> {{ number_format($res->montant, 2) }}</p>
                    <p><strong>Référence :</strong> {{ $res->reference }}</p>
                    <p><strong>Statut paiement :</strong> {{ $res->paiementstatut->titre ?? 'N/A' }}</p>
                </div>
            @endforeach
        </div>
    @endif


          </div> 
          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>
