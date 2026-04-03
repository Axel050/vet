<div wire:ignore x-data="{
    chart: null,
    chartKey: '{{ $chart }}'
}" x-init="const ctx = $refs.canvas;

const formatCurrency = (value) =>
    new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(value);



const centerTextPlugin = {
    id: 'centerText',
    beforeDraw(chart) {
        const { ctx, chartArea } = chart;
        if (!chartArea) return;

        const centerX = (chartArea.left + chartArea.right) / 2;
        const centerY = (chartArea.top + chartArea.bottom) / 2;

        const dataset = chart.data.datasets[0];
        const total = dataset.data.reduce((a, b) => a + b, 0);

        // 👉 sin decimales
        const text = new Intl.NumberFormat('es-AR', {
            style: 'currency',
            currency: 'ARS',
            maximumFractionDigits: 0
        }).format(total);

        ctx.save();

        // texto arriba (label)
        ctx.font = '12px sans-serif';
        ctx.fillStyle = '#9ca3af';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillText('Ingresos', centerX, centerY - 10);

        // monto
        ctx.font = 'bold 14px sans-serif';
        ctx.fillStyle = '#e5e7eb';
        ctx.fillText(text, centerX, centerY + 12);

        ctx.restore();
    }
};



const chartOptions = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: { position: 'bottom' },
        tooltip: {
            callbacks: {
                label: function(context) {
                    const formatCurrency = (value) =>
                        new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(value);

                    return (context.label ? context.label + ': ' : '') + formatCurrency(context.parsed);
                }
            }
        }
    },
    cutout: '75%'
};

chart = new Chart(ctx, {
    type: 'doughnut',
    data: {
        labels: @js($data['labels']),
        datasets: [{
            data: @js($data['data']),
            backgroundColor: @js($data['colors']),
            borderWidth: 0,
            hoverOffset: 4
        }]
    },
    options: chartOptions,
    plugins: [centerTextPlugin]
});

window.addEventListener('update-chart', (event) => {
    const newData = event.detail[0][chartKey];

    if (!newData) return;

    chart.destroy();

    chart = new Chart($refs.canvas, {
        type: 'doughnut',
        data: {
            labels: newData.labels,
            datasets: [{
                data: newData.data,
                backgroundColor: newData.colors,
                borderWidth: 0,
                hoverOffset: 4
            }]
        },
        options: chartOptions,
        plugins: [centerTextPlugin]
    });

});"
    class="w-full flex justify-center items-center h-[240px] relative">
    <canvas x-ref="canvas"></canvas>
</div>
