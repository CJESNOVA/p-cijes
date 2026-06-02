<x-app-layout title="Publier un besoin" is-sidebar-open="true">

<main class="main-content w-full px-[var(--margin-x)] pb-8">

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">
            Publier un besoin
        </h1>
        <p class="text-slate-500">
            Décrivez votre besoin et trouvez rapidement les bons profils
        </p>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    <div class="card p-5">

        <form method="POST" action="{{ route('needs.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">
                    Entreprise
                </label>

                <select name="entreprise_id" class="form-input w-full" required>
                    <option value="">-- Choisir une entreprise --</option>

                    @foreach($entreprises as $entreprise)
                        <option value="{{ $entreprise->id }}">
                            {{ $entreprise->nom }}
                        </option>
                    @endforeach
                </select>
            </div>


<!-- TITRE -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">
                    Titre du besoin
                </label>
                <input type="text"
                       name="title"
                       class="form-input w-full"
                       placeholder="Ex: Recherche plombier pour chantier"
                       required>
            </div>

            <!-- DESCRIPTION -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">
                    Décrivez votre besoin
                </label>
                <textarea name="description"
                          rows="5"
                          class="form-input w-full"
                          placeholder="Expliquez en détail votre besoin..."
                          required></textarea>
            </div>

            <!-- DATE CLOTURE -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">
                    Date de clôture
                </label>
                <input type="date"
                       name="deadline"
                       class="form-input w-full">
            </div>

            <!-- PROFILS -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">
                    Profils recherchés (optionnel)
                </label>
                <input type="text"
                       name="profiles"
                       class="form-input w-full"
                       placeholder="Ex: comptable, designer, développeur...">
            </div>

            <!-- CONDITIONS -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">
                    Conditions d’éligibilité
                </label>
                <textarea name="conditions"
                          rows="3"
                          class="form-input w-full"
                          placeholder="Ex: 3 ans d'expérience minimum..."></textarea>
            </div>

            <!-- FICHIER -->
            <div class="mb-4">
                <label class="block text-sm font-medium mb-1">
                    Pièce jointe (optionnel)
                </label>
                <input type="file"
                       name="file"
                       class="form-input w-full">
            </div>

            <!-- PRIORITY (hidden API) -->
            <input type="hidden" name="priority" value="1">

            <!-- BUTTON -->
            <div class="mt-6 flex justify-end">
                <button type="submit"
                        class="btn bg-blue-600 text-white hover:bg-blue-700">
                    Publier le besoin
                </button>
            </div>

        </form>

    </div>

</main>

</x-app-layout>