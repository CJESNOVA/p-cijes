<x-app-layout title="Mes Prestations" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Mes prestations
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
            <li>Mes entreprises</li>
          </ul> -->
          
<a href="{{ route('prestation.create') }}" class="btn bg-primary text-white hover:bg-primary-focus">Ajouter une prestation</a>
        
</div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
              
        @if(session('success'))
            <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">{{ session('success') }}</div>
        @endif

        
@if($prestations)
              <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-2 lg:gap-6 xl:grid-cols-2">

            @foreach($prestations as $prestation)
                <div class="card p-4 sm:p-5">
                    <h3 class="text-lg font-medium text-slate-700 dark:text-navy-100">{{ $prestation->titre }}</h3>
                    <p>Type: {{ $prestation->prestationtype->titre }}</p>
                    <p>Prix: {{ $prestation->prix }} </p>
                    <p>Durée: {{ $prestation->duree }}</p>
                    <p>{!! $prestation->description !!}</p>
                    <div class="mt-4 flex space-x-2">
                    <a href="{{ route('prestation.edit', $prestation->id) }}" class="btn bg-blue-500 text-white">Modifier</a>
                    <form action="{{ route('prestation.destroy', $prestation->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn bg-red-500 text-white">Supprimer</button>
                    </form>
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

