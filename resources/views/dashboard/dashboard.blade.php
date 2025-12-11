{{-- resources/views/dashboard/dashboard.blade.php --}}
<x-app-layout title="Tableau de bord CJES" is-sidebar-open="false" is-header-blur="true">
    <!-- Main Content Wrapper -->
    <main class="main-content w-full pb-8">

        <div class="mt-6 grid grid-cols-12 gap-4">
            {{-- Left: main modules --}}
            <div class="col-span-12 lg:col-span-8 space-y-6">

                {{-- Top analytics --}}
                @include('dashboard.partials.analytics', ['stats' => $stats ?? []])
        
                {{-- Diagnostics module --}}
                @include('dashboard.partials.diagnostics', ['diagnosticsModules' => $diagnosticsModules ?? collect(), 'entreprises' => $entreprises ?? collect()])

                {{-- Experts module --}}
                @include('dashboard.partials.experts', ['experts' => $experts ?? collect(), 'myExperts' => $myExperts ?? collect()])

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
