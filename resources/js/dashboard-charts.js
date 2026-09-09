// Gráficas del dashboard con ApexCharts.
// La vista solo pasa los datos; aquí vive toda la configuración y el ciclo de vida.

let graficos = [];
let intentosSinApexCharts = 0;

const opcionesSparkline = {
    chart: {
        type: 'area',
        height: 64,
        sparkline: { enabled: true },
        animations: { enabled: false },
    },
    stroke: { curve: 'smooth', width: 2 },
    fill: {
        type: 'gradient',
        gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05, stops: [0, 100] },
    },
    tooltip: {
        fixed: { enabled: false },
        x: { show: false },
        y: { title: { formatter: () => '' } },
        marker: { show: false },
    },
};

const opcionesPrincipal = {
    chart: {
        height: 250,
        type: 'line',
        fontFamily: 'Inter, sans-serif',
        toolbar: { show: false },
        zoom: { enabled: false },
    },
    stroke: { curve: 'smooth', width: [3, 2], dashArray: [0, 5] },
    fill: {
        type: ['gradient', 'solid'],
        gradient: {
            shadeIntensity: 1,
            inverseColors: false,
            opacityFrom: 0.45,
            opacityTo: 0.05,
            stops: [20, 100],
        },
    },
    colors: ['#10B981', '#94A3B8'],
    xaxis: {
        tooltip: { enabled: false },
        axisBorder: { show: false },
        axisTicks: { show: false },
        labels: { style: { colors: '#94A3B8', fontSize: '11px', fontWeight: 600 } },
    },
    yaxis: [
        {
            labels: {
                formatter: (value) => '$' + value.toLocaleString(),
                style: { colors: '#94A3B8', fontSize: '11px', fontWeight: 500 },
            },
        },
        { opposite: true, labels: { show: false } },
    ],
    grid: { borderColor: '#f1f5f9', strokeDashArray: 4, yaxis: { lines: { show: true } } },
    legend: { show: false },
    dataLabels: { enabled: false },
};

/**
 * Dibuja las gráficas con los datos recibidos.
 * Destruye las instancias previas para poder redibujar al cambiar filtros.
 */
export function iniciar(datos) {
    if (typeof ApexCharts === 'undefined') {
        if (++intentosSinApexCharts > 20) {
            mostrarMensajeSinLibreria();
            return;
        }
        setTimeout(() => iniciar(datos), 100);
        return;
    }

    intentosSinApexCharts = 0;
    graficos.forEach((grafico) => grafico.destroy());
    graficos = [];

    const sparklines = [
        ['#sparkVentas', datos.sparklineVentas, '#10B981'],
        ['#sparkFacturas', datos.sparklineFacturas, '#3B82F6'],
        ['#sparkClientes', datos.sparklineClientes, '#8B5CF6'],
        ['#sparkGastos', datos.sparklineGastos, '#F43F5E'],
    ];

    sparklines.forEach(([selector, serie, color]) => {
        crearGrafico(selector, {
            ...opcionesSparkline,
            series: [{ data: serie }],
            colors: [color],
        });
    });

    crearGrafico('#chart', {
        ...opcionesPrincipal,
        series: [
            { name: 'Ingresos', type: 'area', data: datos.chartData.ingresos },
            { name: 'Facturas Emitidas', type: 'line', data: datos.chartData.volumen },
        ],
        labels: datos.chartData.meses,
    });
}

function crearGrafico(selector, opciones) {
    const elemento = document.querySelector(selector);
    if (!elemento) {
        return;
    }

    // render() devuelve una Promise: se guarda la instancia para poder destruirla.
    const grafico = new ApexCharts(elemento, opciones);
    graficos.push(grafico);
    grafico.render();
}

function mostrarMensajeSinLibreria() {
    const area = document.querySelector('#chart');
    if (area && !area.querySelector('.apex-error')) {
        area.innerHTML = '<p class="apex-error text-xs text-slate-400">No se pudo cargar la librería de gráficos.</p>';
    }
}

window.dashboardCharts = { iniciar };