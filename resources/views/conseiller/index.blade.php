<x-app-layout title="Mes prescriptions" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Mes prescriptions
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
        <a href="{{ route('conseiller.create') }}"
                           class="btn bg-warning text-white hover:bg-primary-focus">
                            Prescrire une prestation
                        </a>
                        <a href="{{ route('conseiller.prescrireFormation') }}"
                           class="btn bg-primary text-white hover:bg-primary-focus">
                            Prescrire une formation
                        </a> 
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

        @if(session('success'))
            <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">{{ session('success') }}</div>
        @endif

@if($prescriptions)
              <div class="grid grid-cols-1 gap-4 sm:grid-cols-1 sm:gap-5 lg:grid-cols-1 lg:gap-6 xl:grid-cols-1">

        @if($prescriptions->isEmpty())
            <p class="text-slate-500">Aucune prescription enregistrée.</p>
        @else
                    <div class="is-scrollbar-hidden min-w-full overflow-x-auto">
                        <table class="is-hoverable is-zebra w-full text-left">
                    <thead>
                        <tr>
                            <!-- <th class="whitespace-nowrap rounded-l-lg bg-slate-200 px-3 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">Conseiller</th> -->
                            <th class="whitespace-nowrap rounded-l-lg bg-slate-200 px-3 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">Entreprise / Membre</th>
                            <th class="whitespace-nowrap rounded-l-lg bg-slate-200 px-3 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">Prestation / Formation</th>
                            <th class="whitespace-nowrap rounded-l-lg bg-slate-200 px-3 py-3 font-semibold uppercase text-slate-800 dark:bg-navy-800 dark:text-navy-100 lg:px-5">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($prescriptions as $p)
                            <tr class="border border-transparent border-b-slate-200 dark:border-b-navy-500">
                                <!-- <td class="whitespace-nowrap px-4 py-3 sm:px-5">{{ $p->conseiller->membre->nom ?? '' }} {{ $p->conseiller->membre->prenom ?? '' }}</td> -->
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">{{ $p->entreprise->nom ?? '' }}{{ $p->membre->nom ?? '' }} {{ $p->membre->prenom ?? '' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">{{ $p->prestation->titre ?? '' }}{{ $p->formation->titre ?? '' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 sm:px-5">{{ $p->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
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