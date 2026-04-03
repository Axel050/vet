const fs = require('fs');
const path = require('path');

const filePath = path.resolve('c:/xampp/htdocs/pets/resources/views/components/dashboard/⚡workshop.blade.php');
let content = fs.readFileSync(filePath, 'utf8');

const targetPhpRegex = /        return \[\s*'labels' => \$labels,\s*'data' => \$data,\s*'colors' => array_slice\(\$colors, 0, count\(\$labels\)\)\s*\];\s*\}\s*\};\s*\?>/;

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
};
?>`;

if (content.includes("public function incomeBySpecies()")) {
    console.log("PHP method already exists.");
} else if (targetPhpRegex.test(content)) {
    content = content.replace(targetPhpRegex, replacementPhp);
    console.log("PHP method added.");
} else {
    console.error("PHP target not found!");
}

fs.writeFileSync(filePath, content, 'utf8');
