<?php

namespace Database\Seeders;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

class SupplierSeeder extends Seeder
{
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Nordic Timber Co.', 
                'identifier' => 'SUP-2023-001',
                'layups' => [
                    ['name' => 'Nordic Wall-120', 'identifier' => 'NW-120', 'layers' => 3],
                    ['name' => 'Nordic Floor-200', 'identifier' => 'NF-200', 'layers' => 5],
                ]
            ],
            [
                'name' => 'Alpine CLT Solutions', 
                'identifier' => 'SUP-2023-042',
                'layups' => [
                    ['name' => 'Alpine-X 150', 'identifier' => 'AX-150', 'layers' => 3],
                    ['name' => 'Alpine-X 240 Premium', 'identifier' => 'AX-240', 'layers' => 7],
                ]
            ],
            [
                'name' => 'MassivWood Ltd.', 
                'identifier' => 'SUP-2024-012',
                'layups' => [
                    ['name' => 'Massiv Wall Standard', 'identifier' => 'MWS-100', 'layers' => 3],
                    ['name' => 'Massiv Floor Heavy', 'identifier' => 'MFH-300', 'layers' => 9],
                ]
            ],
            [
                'name' => 'TimberStruct Inc.', 
                'identifier' => 'SUP-2024-088',
                'layups' => [
                    ['name' => 'TS-Wall Eco', 'identifier' => 'TSW-ECO', 'layers' => 3],
                    ['name' => 'TS-Roof Light', 'identifier' => 'TSR-LITE', 'layers' => 3],
                ]
            ],
            [
                'name' => 'EuroLam Systems', 
                'identifier' => 'SUP-2024-099',
                'layups' => [
                    ['name' => 'EuroWall 140', 'identifier' => 'EW-140', 'layers' => 5],
                    ['name' => 'EuroFloor 220', 'identifier' => 'EF-220', 'layers' => 5],
                ]
            ],
        ];

        foreach ($suppliers as $supplierData) {
            $layupsData = $supplierData['layups'];
            unset($supplierData['layups']);
            
            $supplier = Supplier::create($supplierData);

            foreach ($layupsData as $config) {
                $layup = CltLayup::create([
                    'supplier_id' => $supplier->id,
                    'name' => $config['name'],
                    'identifier' => $config['identifier'],
                ]);

                for ($j = 1; $j <= $config['layers']; $j++) {
                    $isOdd = $j % 2 === 1;
                    // Vary thickness slightly per layer for realism
                    $thickness = $isOdd ? 40.00 : 20.00;
                    if ($j === 1 || $j === $config['layers']) {
                        $thickness += 2.00; // Outer layers slightly thicker
                    }

                    CltLayer::create([
                        'layup_id' => $layup->id,
                        'layer_order' => $j,
                        'thickness' => $thickness,
                        'width' => 1200.00,
                        'angle' => $isOdd ? 0.00 : 90.00,
                    ]);
                }
            }
        }
    }
}
