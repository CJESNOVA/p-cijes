<x-app-layout title="Mes Pièces administratives" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Mes Pieces administratives
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
            <li>Mes Pieces</li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">

    <form action="{{ route('pieces.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

    @if (session('success'))
        <div class="alert flex rounded-lg bg-success px-4 py-4 text-white sm:px-5 mb-3">{{ session('success') }}</div>
    @endif


          <!-- Input Validation -->
          <div class="card px-4 pb-4 sm:px-5">
            <div class="max-w-xxl">
              <div
                x-data="pages.formValidation.initFormValidationExample"
                class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:gap-6"
              >

                
            <div class="max-w-xxl">
                  <label class="block">
                    <span>Entreprise </span>
                <select name="entreprise_id" 
      class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2 hover:border-slate-400 focus:border-primary dark:border-navy-450 dark:bg-navy-700 dark:hover:border-navy-400 dark:focus:border-accent" required>
                    <option value="">-- Choisir --</option>
                    @foreach ($entreprises as $entreprise)
                        <option value="{{ $entreprise->id }}" {{ old('entreprise_id', $entreprise->entreprise_id ?? '') == $entreprise->id ? 'selected' : '' }}>
                            {{ $entreprise->nom }}
                        </option>
                    @endforeach
                </select>
                  </label>
                </div>

              <div>
            </div>

        @foreach ($piecetypes as $piecetype)
            @php
                $existing = $pieces[$piecetype->id] ?? null;
            @endphp

                
            <div class="max-w-xxl">
                    <span>{{ $piecetype->titre }}</span>
              <div class="mt-1">
                <label
    class="btn bg-slate-150 font-medium text-slate-800 hover:bg-slate-200 focus:bg-slate-200 active:bg-slate-200/80 dark:bg-navy-500 dark:text-navy-50 dark:hover:bg-navy-450 dark:focus:bg-navy-450 dark:active:bg-navy-450/90"
  >
    <input class="form-control"
      type="file"
      name="piece_{{ $piecetype->id }}"
    />
    <!-- <div class="flex items-center space-x-2">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="size-5"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"
        />
      </svg>
      <span>Choose File</span>
    </div> -->
  </label>
  

              </div>
            </div>

                


                <div>
                  <!-- <label class="block">
                    
                @if ($existing)
                    <p class="mb-1 text-sm text-gray-600">
                        Piece existant :
                        <a href="{{ env('APP_URL') . 'storage/' . $existing->fichier }}" target="_blank" class="text-blue-600 underline">
                            Voir le piece
                        </a>
                    </p>
                @endif
                    
                </label> -->
                </div>

                
        @endforeach



                <div>
                  <button type="submit"
    class="btn bg-primary font-medium text-white hover:bg-primary-focus focus:bg-primary-focus active:bg-primary-focus/90 dark:bg-accent dark:hover:bg-accent-focus dark:focus:bg-accent-focus dark:active:bg-accent/90"
  >
    Enregistrer les pieces
  </button>
                </div>    

              </div>
            </div>
            
          </div>     

    </form>

                {{-- ✅ Liste de toutes les pièces déjà enregistrées --}}
                <div class="card mt-6 p-4">
                    <h3 class="text-lg font-semibold mb-3">Toutes mes pièces enregistrées</h3>

                    @if($pieces->isEmpty())
                        <p class="text-slate-500">Aucune pièce enregistrée pour vos entreprises.</p>
                    @else
                        <div class="is-scrollbar-hidden min-w-full overflow-x-auto">
                            <table class="is-hoverable w-full text-left">
                                <thead>
                                    <tr>
                                        <th class="px-3 py-2">Entreprise</th>
                                        <th class="px-3 py-2">Type de pièce</th>
                                        <th class="px-3 py-2">Fichier</th>
                                        <th class="px-3 py-2">Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pieces as $piece)
                                        <tr class="border-b">
                                            <td class="px-3 py-2">{{ $piece->entreprise->nom ?? '—' }}</td>
                                            <td class="px-3 py-2">{{ $piece->piecetype->titre ?? '—' }}</td>
                                            <td class="px-3 py-2">
                                                <a href="{{ env('APP_URL') . 'storage/' . $piece->fichier }}" target="_blank"
                                                   class="text-blue-600 underline">Voir</a>
                                            </td>
                                            <td class="px-3 py-2">{{ \Carbon\Carbon::parse($piece->datedocument)->format('d/m/Y') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
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
