<x-app-layout title="Mes profils experts" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Mes profils experts
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
            <li>Mes profils experts</li>
          </ul> -->

      <div class="flex space-x-3">
        <a href="{{ route('expert.form') }}"
           class="btn bg-primary text-white hover:bg-primary-focus">Devenir un expert</a>
        <a href="{{ route('expert.plans.index') }}"
           class="btn bg-info text-white hover:bg-info-focus">Voir les plans disponibles</a>
      </div>

        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

    @if(session('success'))
      <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5">{{ session('success') }}</div>
    @endif
          

@if($experts)
              <div class="grid grid-cols-2 gap-4 sm:grid-cols-2 sm:gap-5 lg:grid-cols-2 lg:gap-6 xl:grid-cols-2">

      @foreach($experts as $em)
            <div class="card grow items-center p-4 sm:p-5">
                <div class="avatar size-20">
                  @if($em->membre && $em->membre->vignette)
                    <img class="rounded-full " src="{{ env('SUPABASE_BUCKET_URL') . '/' . $em->membre->vignette }}" alt="avatar" />
                    @endif
                  @if($em->membre && $em->membre->spotlight)
                    <div
                        class="absolute right-0 m-1 size-4 rounded-full border-2 border-white bg-primary dark:border-navy-700 dark:bg-accent">
                    </div>
                    @endif
                </div>
                <h3 class="pt-3 text-lg font-medium text-slate-700 dark:text-navy-100">
                    {{ $em->membre->nom }} {{ $em->membre->prenom }}
                </h3>
                <p class="text-xs-plus">{{ $em->domaine }}</p>
                <div class="my-4 h-px w-full bg-slate-200 dark:bg-navy-500"></div>
                <div class="grow space-y-4">
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex size-7 items-center rounded-lg bg-primary/10 p-2 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                            <i class="fa fa-user text-xs"></i>
                        </div>
                        <p>{{ $em->experttype->titre }}</p>
                    </div>
                </div>
                <div class="my-4 h-px w-full bg-slate-200 dark:bg-navy-500"></div>
                <div class="grow space-y-4">
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex size-7 items-center rounded-lg bg-primary/10 p-2 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                            <i class="fa fa-phone text-xs"></i>
                        </div>
                        <p>{{ $em->membre->telephone }}</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div
                            class="flex size-7 items-center rounded-lg bg-primary/10 p-2 text-primary dark:bg-accent-light/10 dark:text-accent-light">
                            <i class="fa fa-envelope text-xs"></i>
                        </div>
                        <p>{{ $em->membre->email }}</p>
                    </div>
                </div>
               
               <a href="{{ route('expert.show', $em->id) }}"
               class="btn mt-5 space-x-2 rounded-full bg-info font-medium text-white hover:bg-info-focus focus:bg-info-focus active:bg-info-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">Détail</a>
                
                <a href="{{ route('expert.edit', $em->id) }}"
               class="btn mt-5 space-x-2 rounded-full bg-warning font-medium text-white hover:bg-warning-focus focus:bg-warning-focus active:bg-warning-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">Modifier</a>
               
            <form action="{{ route('expert.destroy', $em->id) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn mt-5 space-x-2 rounded-full bg-error font-medium text-white hover:bg-error-focus focus:bg-error-focus active:bg-error-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90">Supprimer</button>
            </form>

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
