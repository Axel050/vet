const fs = require('fs');
const path = require('path');

const filePath = path.resolve('c:/xampp/htdocs/pets/resources/views/components/dashboard/⚡workshop.blade.php');
let content = fs.readFileSync(filePath, 'utf8');

const target1 = `<div class="w-full flex justify-center items-center h-[240px] relative" x-data="incomePieChart" x-init="initChart()">
                    <canvas id="incomePieCanvas"></canvas>
                </div>`;

const replacement1 = `<div class="w-full flex justify-center items-center h-[240px] relative" 
                     x-data="{ chartData: @js($this->incomeByType()) }" 
                     x-init="
                        setTimeout(() => {
                            if (!window.Chart) return;
                            const ctx = $refs.canvas;
                            if (!ctx) return;
                            
                            const formatCurrency = (value) => new Intl.NumberFormat('es-AR', { style: 'currency', currency: 'ARS' }).format(value);
                            Chart.defaults.color = '#9ca3af';
                            
                            new Chart(ctx, {
                                type: 'doughnut',
                                data: {
                                    labels: chartData.labels,
                                    datasets: [{
                                        data: chartData.data,
                                        backgroundColor: chartData.colors,
                                        borderWidth: 0,
                                        hoverOffset: 4
                                    }]
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    plugins: {
                                        legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20, font: { size: 11 } } },
                                        tooltip: {
                                            backgroundColor: 'rgba(17, 24, 39, 0.95)',
                                            titleColor: '#fff', bodyColor: '#fff',
                                            borderColor: 'rgba(75, 85, 99, 0.4)', borderWidth: 1, padding: 12, boxPadding: 6, usePointStyle: true,
                                            callbacks: {
                                                label: function(context) {
                                                    return (context.label ? context.label + ': ' : '') + formatCurrency(context.parsed);
                                                }
                                            }
                                        }
                                    },
                                    cutout: '75%'
                                }
                            });
                        }, 100);
                     ">
                    <canvas x-ref="canvas"></canvas>
                </div>`;

const target1Regex = /<div class="w-full flex justify-center items-center h-\[240px\] relative" x-data="incomePieChart" x-init="initChart\(\)">[\s\S]*?<canvas id="incomePieCanvas"><\/canvas>[\s\S]*?<\/div>/;

if (target1Regex.test(content)) {
    content = content.replace(target1Regex, replacement1);
    console.log("Chunk 1 replaced successfully.");
} else {
    console.error("Chunk 1 not found!");
}

const target2Regex = /<script>[\s\S]*?document\.addEventListener\('alpine:init'[\s\S]*?<\/script>/;

if (target2Regex.test(content)) {
    content = content.replace(target2Regex, '');
    console.log("Chunk 2 replaced successfully.");
} else {
    console.error("Chunk 2 not found!");
}

fs.writeFileSync(filePath, content, 'utf8');
console.log("File saved.");
