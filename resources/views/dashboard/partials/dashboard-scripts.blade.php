{{-- resources/views/dashboard/partials/dashboard-scripts.blade.php --}}
<!-- ApexCharts et autres librairies (CDN ou assets) -->
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    // Mini earning chart
    var optionsEarning = {
        chart: { type: 'area', height: 120, toolbar: { show: false } },
        series: [{ name: 'Revenu', data: {!! json_encode($stats['earning_series'] ?? [10,20,15,30,25,40]) !!} }],
        stroke: { curve: 'smooth' },
        colors: ['#6366F1'],
        grid: { show: false },
        tooltip: { enabled: true },
        xaxis: { labels: { show: false }, axisBorder: { show: false } },
        yaxis: { show: false }
    };
    var chart = new ApexCharts(document.querySelector('#chart-earning'), optionsEarning);
    chart.render();

    // Mini calendar placeholder (you can replace with FullCalendar)
    const miniCalendar = document.getElementById('mini-calendar');
    if (miniCalendar) {
        miniCalendar.innerHTML = '<div class="h-full flex items-center justify-center text-sm text-slate-400">Agenda</div>';
    }

    // Listen range change event (example)
    window.addEventListener('change-range', function(e) {
        // TODO: fetch new stats via AJAX and update charts
        console.log('change-range', e.detail);
    });
});
</script>
