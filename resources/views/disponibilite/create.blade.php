<x-app-layout title="Mes disponibilités"  is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <div class="flex items-center space-x-4 py-5 lg:py-6">
          <h2
            class="text-xl font-medium text-slate-800 dark:text-navy-50 lg:text-2xl"
          >
            Définir ma disponibilité
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
            <li>Définir ma disponibilité </li>
          </ul> -->
        </div>
        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">


        @if (session('success'))
            <div class="alert bg-success text-white px-4 py-2 rounded mb-3">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert bg-error text-white px-4 py-2 rounded mb-3">
                {{ session('error') }}
            </div>
        @endif

                <!-- Formulaire -->
                <form action="{{ route('disponibilite.store') }}" method="POST">
                    @csrf

          <div class="card px-4 pb-4 sm:px-5">
            <div class="max-w-xxl">
              <div
                x-data="pages.formValidation.initFormValidationExample"
                class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-5 lg:gap-6"
              >

              

                        <div>
                            <label class="block">
                                <span>Expert (domaine)</span>
                                <select name="expert_id"
                                    class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2"
                                    required>
                                    <option value="">-- Choisir un domaine --</option>
                                    @foreach ($experts as $expert)
                                        <option value="{{ $expert->id }}">
                                            {{ $expert->domaine ?? 'Domaine inconnu' }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>
                        </div>

                        <div>
                            <label class="block">
                                <span>Jour</span>
                                <select name="jour_id"
                                    class="form-select mt-1.5 w-full rounded-lg border border-slate-300 bg-white px-3 py-2"
                                    required>
                                    <option value="">-- Choisir un jour --</option>
                                    @foreach ($jours as $jour)
                                        <option value="{{ $jour->id }}">{{ $jour->titre }}</option>
                                    @endforeach
                                </select>
                            </label>
                        </div>

                        <div>
                            <label class="block">
                                <span>Heure de début</span>
                                <input type="time" name="horairedebut" required
                                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2" />
                            </label>
                        </div>

                        <div>
                            <label class="block">
                                <span>Heure de fin</span>
                                <input type="time" name="horairefin" required
                                    class="form-input mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-2" />
                            </label>
                        </div>

                        <div>
                            <button type="submit"
                                class="btn bg-primary text-white hover:bg-primary-focus">
                                Ajouter une disponibilité
                            </button>
                        </div>

                    </div>
                    </div>
                    </div>
                </form>

                <!-- Liste des disponibilités -->
                <div class="card mt-6 p-4">
                    <h3 class="text-lg font-semibold mb-3">Mes disponibilités enregistrées</h3>

                    @if ($disponibilites->isEmpty())
                        <p class="text-slate-500">Aucune disponibilité enregistrée.</p>
                    @else
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-200 dark:bg-navy-700">
                                    <th class="px-3 py-2">Domaine</th>
                                    <th class="px-3 py-2">Jour</th>
                                    <th class="px-3 py-2">Début</th>
                                    <th class="px-3 py-2">Fin</th>
                                    <th class="px-3 py-2">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($disponibilites as $d)
                                    <tr class="border-b">
                                        <td class="px-3 py-2">{{ $d->expert->domaine ?? '—' }}</td>
                                        <td class="px-3 py-2">{{ $d->jour->titre ?? '—' }}</td>
                                        <td class="px-3 py-2">{{ $d->horairedebut }}</td>
                                        <td class="px-3 py-2">{{ $d->horairefin }}</td>
                                        <td class="px-3 py-2">
                                            <form action="{{ route('disponibilite.destroy', $d->id) }}" method="POST"
                                                onsubmit="return confirm('Supprimer cette disponibilité ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn bg-error text-white">Supprimer</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

            </div>

            <div class="col-span-12 lg:col-span-4">
                @include('layouts.sidebar')
            </div>
        </div>
    </main>
</x-app-layout>
