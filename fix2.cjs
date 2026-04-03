const fs = require('fs');
const path = require('path');

const filePath = path.resolve('c:/xampp/htdocs/pets/resources/views/components/dashboard/⚡workshop.blade.php');
let content = fs.readFileSync(filePath, 'utf8');

// 1. Add PHP method incomeBySpecies() after incomeByType()
const targetPhp = `        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => array_slice($colors, 0, count($labels))
        ];
    }
};`;

const replacementPhp = `        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => array_slice($colors, 0, count($labels))
        ];
    }

    #[Computed]
    public function incomeBySpecies()
    {
        $records = \\App\\Models\\MedicalRecord::with('pet.species')
            ->where('veterinary_id', auth()->user()->veterinary_id)
            ->where('price', '>', 0)
            ->get();

        $grouped = [];
        foreach ($records as $record) {
            $speciesName = 'Otro';
            if ($record->pet) {
                if ($record->pet->species) {
                    $speciesName = $record->pet->species->name;
                } elseif ($record->pet->specie_custom) {
                    $speciesName = $record->pet->specie_custom;
                }
            }

            if (!isset($grouped[$speciesName])) {
                $grouped[$speciesName] = 0;
            }
            $grouped[$speciesName] += (float) $record->price;
        }

        $labels = array_keys($grouped);
        $data = array_values($grouped);
        
        $colors = ['#10b981', '#f59e0b', '#3b82f6', '#8b5cf6', '#ec4899', '#ef4444', '#6b7280'];

        if (empty($labels)) {
            return [
                'labels' => ['Sin Ingresos'],
                'data' => [0],
                'colors' => ['#374151']
            ];
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => array_slice($colors, 0, count($labels))
        ];
    }
};`;

if (content.includes("public function incomeBySpecies()")) {
    console.log("PHP method already exists.");
} else if (content.includes(targetPhp)) {
    content = content.replace(targetPhp, replacementPhp);
    console.log("PHP method added.");
} else {
    console.error("PHP target not found!");
}

// 2. Replace the HTML Grid
const targetHtml = /<!-- Dashboard Charts -->\s*<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:mb-6 mb-4 mt-2">[\s\S]*?<!-- Future Graph Placeholder \/ Filler -->[\s\S]*?<\/div>\s*<\/div>/;

const replacementHtml = `<!-- Dashboard Charts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:mb-6 mb-4 mt-2">
            <!-- Pie Chart -->
            <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-lg md:p-6 p-4">
                <h3 class="text-sm font-semibold text-gray-300 mb-4 flex justify-between items-center">
                    Ingresos por Tipo
                    <span class="text-xs bg-indigo-500/20 text-indigo-400 px-2 py-1 rounded">Global</span>
                </h3>
                <div class="w-full flex justify-center items-center h-[240px] relative" 
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
                </div>
            </div>

            <!-- Pie Chart Species -->
            <div class="bg-gray-800 rounded-xl border border-gray-700 shadow-lg md:p-6 p-4">
                <h3 class="text-sm font-semibold text-gray-300 mb-4 flex justify-between items-center">
                    Ingresos por Especies
                    <span class="text-xs bg-emerald-500/20 text-emerald-400 px-2 py-1 rounded">Global</span>
                </h3>
                <div class="w-full flex justify-center items-center h-[240px] relative" 
                     x-data="{ chartData: @js($this->incomeBySpecies()) }" 
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
                </div>
            </div>
        </div>`;

if (targetHtml.test(content)) {
    content = content.replace(targetHtml, replacementHtml);
    console.log("HTML Grid replaced.");
} else {
    console.error("HTML Grid not found!");
}

fs.writeFileSync(filePath, content, 'utf8');
console.log("Done");
