<x-app-layout title="Mes Récompenses" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-[#152737] to-[#152737] flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v13m0-13V6a2 2 0 112 2h-2zm0 0V5.5A2.5 2.5 0 109.5 8H12zm-7 4h14M5 12a2 2 0 110-4h14a2 2 0 110 4M5 12v7a2 2 0 002 2h10a2 2 0 002-2v-7"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Mes récompenses
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Découvrez vos récompenses et cadeaux disponibles
                    </p>
                </div>
            </div>
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
              
                <!-- Messages modernes -->
                @if(session('success'))
                    <div class="alert flex rounded-lg bg-green-500 px-6 py-4 text-white mb-6 shadow-lg">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ session('success') }}
                    </div>
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