<x-app-layout title="Sélection de catégorie de diagnostic" is-sidebar-open="true" is-header-blur="true">
    <main class="main-content w-full px-[var(--margin-x)] pb-8">
        <!-- Header moderne -->
        <div class="mb-2">
            <div class="flex items-center gap-4 mb-2">
                <div class="h-14 w-14 rounded-xl bg-gradient-to-br from-[#4FBE96] to-[#4FBE96] flex items-center justify-center shadow-lg">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold text-slate-800 dark:text-navy-50">
                        Sélectionner une catégorie
                    </h1>
                    <p class="mt-2 text-slate-600 dark:text-navy-200 text-lg">
                        Choisissez la catégorie de diagnostic que vous souhaitez compléter
                    </p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-12 lg:gap-6">
            <div class="col-span-12 pt-6 lg:col-span-8 lg:pb-6">
                <div class="card px-4 pb-4 sm:px-5">
                    <div class="max-w-xxl">
                        
                        @if(session('error'))
                            <div class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                                {{ session('error') }}
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg">
                                {{ session('success') }}
                            </div>
                        @endif


                        <!-- Catégories disponibles -->
                        <h3 class="text-xl font-semibold text-slate-800 mb-6">
                            <i class="fas fa-th-large mr-2 text-[#4FBE96]"></i>
                            Catégories de diagnostic disponibles
                        </h3>

                        @if($categories->count() > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                                @foreach($categories as $category)
                                    <div class="bg-white border border-slate-200 rounded-lg p-6 hover:shadow-lg transition-shadow duration-200 cursor-pointer group"
                                         onclick="window.location.href='{{ route('diagnostic.form', ['categoryId' => $category->id]) }}'">
                                        
                                        <!-- Icône de la catégorie -->
                                        <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-[#4FBE96]/20 to-[#4FBE96]/10 flex items-center justify-center mb-4 group-hover:scale-110 transition-transform duration-200">
                                            @if($category->id == 1)
                                                <i class="fas fa-building text-2xl text-[#4FBE96]"></i>
                                            @elseif($category->id == 2)
                                                <i class="fas fa-industry text-2xl text-[#4FBE96]"></i>
                                            @else
                                                <i class="fas fa-cube text-2xl text-[#4FBE96]"></i>
                                            @endif
                                        </div>

                                        <!-- Titre et description -->
                                        <h4 class="text-lg font-semibold text-slate-800 mb-2 group-hover:text-[#4FBE96] transition-colors duration-200">
                                            {{ $category->titre }}
                                        </h4>
                                        

                                        <!-- Statistiques -->
                                        <div class="flex items-center justify-between text-sm">
                                            <div class="flex items-center text-[#4FBE96]">
                                                <i class="fas fa-cube mr-1"></i>
                                                <span class="font-medium">{{ $category->diagnosticmodules_count }} modules</span>
                                            </div>
                                            <div class="text-slate-500">
                                                <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform duration-200"></i>
                                            </div>
                                        </div>

                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-12 bg-slate-50 rounded-lg">
                                <i class="fas fa-exclamation-triangle text-4xl text-yellow-500 mb-4"></i>
                                <h4 class="text-lg font-semibold text-slate-800 mb-2">
                                    Aucune catégorie disponible
                                </h4>
                                <p class="text-slate-600">
                                    Il n'y a actuellement aucune catégorie de diagnostic disponible. 
                                    Veuillez contacter l'administrateur pour plus d'informations.
                                </p>
                            </div>
                        @endif

                        
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-span-12 py-6 lg:sticky lg:bottom-0 lg:col-span-4 lg:self-end">
                @include('layouts.sidebar')
            </div>
        </div>
    </main>
</x-app-layout>
