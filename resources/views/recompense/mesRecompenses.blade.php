<x-app-layout title="Mes Récompenses" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Mes Récompenses
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
            <li>Mes Récompenses</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
              

    @if(session('success'))
      <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">{{ session('success') }}</div>
    @endif
          
@if($recompenses->isEmpty())
    <div class="text-center py-8 text-slate-500 dark:text-navy-200">
        <i class="fa fa-gift text-3xl mb-3 text-slate-400 dark:text-navy-300"></i>
        <p class="text-base font-medium">Aucune récompense pour l’instant.</p>
    </div>
@else
    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-2">
        @foreach($recompenses as $r)
            <div class="card border border-slate-200 dark:border-navy-700 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 bg-white dark:bg-navy-800">
                <div class="card-body p-5 flex flex-col space-y-3">
                    <div class="flex items-center space-x-3">
                        <div class="flex size-10 items-center justify-center rounded-full bg-success/10">
                            <i class="fa fa-award text-success text-lg"></i>
                        </div>
                        <h4 class="font-semibold text-slate-700 dark:text-navy-100">
                            {{ $r->commentaire }}
                        </h4>
                    </div>

                    <div class="flex justify-between text-sm text-slate-500 dark:text-navy-300 mt-2">
                        <span>
                            <i class="fa fa-coins mr-1 text-warning"></i>
                            <strong>{{ $r->valeur }}</strong> pts
                        </span>
                        <span class="italic text-xs">
                            <i class="fa fa-clock mr-1"></i>
                            {{ \Carbon\Carbon::parse($r->updated_at)->diffForHumans() }}
                        </span>
                    </div>

                    @if(!empty($r->action))
                        <div class="mt-2 text-xs text-slate-400 dark:text-navy-200">
                            <i class="fa fa-tag mr-1 text-primary"></i>
                            {{ $r->action->titre ?? 'Action liée' }}
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endif


          </div> 

            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>    

        </div>
      </main>
</x-app-layout>