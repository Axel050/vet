<div wire:ignore x-data="{
    chart: null,
    chartKey: '{{ $chart }}',

    // Función para procesar y ordenar los datos
    getSortedData(labels, data, colors) {
        const combined = labels.map((l, i) => ({ label: l, val: data[i], col: colors[i] }));
        combined.sort((a, b) => b.val - a.val);
        return {
            labels: combined.map(x => x.label),
            data: combined.map(x => x.val),
            colors: combined.map(x => x.col)
        };
    },

    initChart(el, labels, data, colors) {
        const sorted = this.getSortedData(labels, data, colors);
        const formatCurrency = (v) => new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS', maximumFractionDigits: 0 }).format(v);

        return new Chart(el, {
            type: 'bar',
            data: {
                labels: sorted.labels,
                datasets: [{
                    data: sorted.data,
                    backgroundColor: sorted.colors,
                    borderRadius: 4,
                    barThickness: 'flex',
                    maxBarThickness: 25
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: (context) => ' ' + formatCurrency(context.parsed.x)
                        }
                    }
                },
                scales: {
                    x: {
                        beginAtZero: true,
                        grid: { color: 'rgba(156, 163, 175, 0.05)', drawBorder: false },
                        ticks: {
                            color: '#9ca3af',
                            font: { size: 10 },
                            callback: (v) => formatCurrency(v)
                        }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { color: '#e5e7eb', font: { size: 11 } }
                    }
                }
            }
        });
    }
}" x-init="chart = initChart($refs.canvas, @js($data['labels']), @js($data['data']), @js($data['colors']));

window.addEventListener('update-chart', (event) => {
    const newData = event.detail[0][chartKey];
    if (!newData) return;

    // Si ya existe un gráfico, lo destruimos por completo para evitar errores de stack/memoria
    if (chart) {
        chart.destroy();
    }

    // Creamos uno nuevo con los datos actualizados
    chart = initChart($refs.canvas, newData.labels, newData.data, newData.colors);
});" class="w-full h-[320px] px-2 pb-4">
    <canvas x-ref="canvas"></canvas>
</div>
