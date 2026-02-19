{{-- resources/views/dashboard/dashboard.blade.php --}}
<x-app-layout title="Tableau de bord CJES" is-sidebar-open="false" is-header-blur="true">
    <!-- Main Content Wrapper -->
    <main class="main-content w-full pb-8">

        <div class="mt-6 grid grid-cols-12 gap-4">
            {{-- Left: main modules --}}
            <div class="col-span-12 lg:col-span-8 space-y-6">

        {{-- Messages d'alerte --}}
        @if(session('success'))
            <div class="alert flex rounded-lg bg-[#4FBE96] px-6 py-4 text-white mb-6 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert flex rounded-lg bg-red-500 px-6 py-4 text-white mb-6 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('error') }}
            </div>
        @endif

        @if(session('info'))
            <div class="alert flex rounded-lg bg-blue-500 px-6 py-4 text-white mb-6 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                {{ session('info') }}
            </div>
        @endif

        @if(session('warning'))
            <div class="alert flex rounded-lg bg-yellow-500 px-6 py-4 text-white mb-6 shadow-lg">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                {{ session('warning') }}
            </div>
        @endif

                {{-- Top analytics --}}
                @include('dashboard.partials.analytics', ['stats' => $stats ?? []])
        
                {{-- Diagnostics module --}}
                @include('dashboard.partials.diagnostics', ['diagnosticsModules' => $diagnosticsModules ?? collect(), 'entreprises' => $entreprises ?? collect()])

                {{-- Experts module --}}
                {{-- @include('dashboard.partials.experts', ['experts' => $experts ?? collect(), 'myExperts' => $myExperts ?? collect()]) --}}

                {{-- Réservations / Disponibilités --}}
                @include('dashboard.partials.reservations', ['reservations' => $reservations ?? collect()])

            </div>

            {{-- Right: sidebar widgets --}}
            <div class="col-span-12 lg:col-span-4 space-y-6">

                {{-- Entreprises module --}}
                @include('dashboard.partials.entreprises', ['entreprises' => $entreprises ?? collect()])
                
                @include('dashboard.partials.calendar', ['calendarEvents' => $calendarEvents ?? collect()]){{--  --}}

                @include('dashboard.partials.messages', ['messages' => $messages ?? collect()])
                
                {{-- @include('dashboard.partials.system_health') --}}

            </div>
        </div>
    </main>
</x-app-layout>

@section('scripts')
    @include('dashboard.partials.dashboard-scripts')
@endsection
