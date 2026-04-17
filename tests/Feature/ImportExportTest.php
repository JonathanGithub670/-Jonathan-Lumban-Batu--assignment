<?php

namespace Tests\Feature;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Models\User;
use App\Services\ImportExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ImportExportTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->supplier = Supplier::factory()->create(['name' => 'Test Supplier']);
    }

    public function test_can_export_supplier_data(): void
    {
        $layup = CltLayup::factory()->create([
            'supplier_id' => $this->supplier->id,
            'name' => 'Test Layup',
        ]);
        CltLayer::factory()->create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 20.00,
            'width' => 100.00,
            'angle' => 0.00,
        ]);

        $response = $this->actingAs($this->user)->get(route('suppliers.export', $this->supplier));

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertEquals('Test Supplier', $data['supplier']['name']);
        $this->assertCount(1, $data['layups']);
        $this->assertEquals('Test Layup', $data['layups'][0]['name']);
        $this->assertCount(1, $data['layups'][0]['layers']);
    }

    public function test_can_import_new_layups(): void
    {
        $importData = [
            'supplier' => ['name' => 'Test Supplier'],
            'layups' => [
                [
                    'name' => 'New Layup',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 20.0, 'width' => 100.0, 'angle' => 0.0],
                        ['layer_order' => 2, 'thickness' => 30.0, 'width' => 150.0, 'angle' => 90.0],
                    ],
                ],
            ],
        ];

        $file = UploadedFile::fake()->createWithContent('import.json', json_encode($importData));

        $response = $this->actingAs($this->user)->post(
            route('suppliers.import.analyze', $this->supplier),
            ['file' => $file]
        );

        $response->assertRedirect(route('suppliers.show', $this->supplier));
        $this->assertDatabaseHas('clt_layups', ['name' => 'New Layup', 'supplier_id' => $this->supplier->id]);
        $this->assertEquals(2, CltLayer::count());
    }

    public function test_import_detects_conflicts(): void
    {
        $layup = CltLayup::factory()->create([
            'supplier_id' => $this->supplier->id,
            'name' => 'Existing Layup',
        ]);
        CltLayer::factory()->create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 20.00,
            'width' => 100.00,
            'angle' => 0.00,
        ]);

        $importData = [
            'supplier' => ['name' => 'Test Supplier'],
            'layups' => [
                [
                    'name' => 'Existing Layup',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 25.0, 'width' => 100.0, 'angle' => 0.0],
                    ],
                ],
            ],
        ];

        $file = UploadedFile::fake()->createWithContent('import.json', json_encode($importData));

        $response = $this->actingAs($this->user)->post(
            route('suppliers.import.analyze', $this->supplier),
            ['file' => $file, 'strategy' => 'manual']
        );

        $response->assertStatus(200);
        $response->assertViewIs('suppliers.conflicts');
    }

    public function test_import_export_service_overwrite_strategy(): void
    {
        $layup = CltLayup::factory()->create([
            'supplier_id' => $this->supplier->id,
            'name' => 'Existing Layup',
        ]);
        CltLayer::factory()->create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 20.00,
            'width' => 100.00,
            'angle' => 0.00,
        ]);

        $importData = [
            'layups' => [
                [
                    'name' => 'Existing Layup',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 50.0, 'width' => 200.0, 'angle' => 45.0],
                    ],
                ],
            ],
        ];

        $service = app(ImportExportService::class);
        $result = $service->executeImport($this->supplier, $importData, 'overwrite');

        $this->assertEquals(1, $result['updated']);
        $this->assertDatabaseHas('clt_layers', [
            'layup_id' => $layup->id,
            'thickness' => 50.0,
            'width' => 200.0,
            'angle' => 45.0,
        ]);
    }

    public function test_import_export_service_skip_strategy(): void
    {
        $layup = CltLayup::factory()->create([
            'supplier_id' => $this->supplier->id,
            'name' => 'Existing Layup',
        ]);
        CltLayer::factory()->create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 20.00,
            'width' => 100.00,
            'angle' => 0.00,
        ]);

        $importData = [
            'layups' => [
                [
                    'name' => 'Existing Layup',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 50.0, 'width' => 200.0, 'angle' => 45.0],
                    ],
                ],
            ],
        ];

        $service = app(ImportExportService::class);
        $result = $service->executeImport($this->supplier, $importData, 'skip');

        $this->assertEquals(1, $result['skipped']);
        $this->assertDatabaseHas('clt_layers', [
            'layup_id' => $layup->id,
            'thickness' => 20.00,
            'width' => 100.00,
            'angle' => 0.00,
        ]);
    }

    public function test_import_export_service_duplicate_strategy(): void
    {
        $layup = CltLayup::factory()->create([
            'supplier_id' => $this->supplier->id,
            'name' => 'Existing Layup',
        ]);
        CltLayer::factory()->create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 20.00,
            'width' => 100.00,
            'angle' => 0.00,
        ]);

        $importData = [
            'layups' => [
                [
                    'name' => 'Existing Layup',
                    'layers' => [
                        ['layer_order' => 1, 'thickness' => 50.0, 'width' => 200.0, 'angle' => 45.0],
                    ],
                ],
            ],
        ];

        $service = app(ImportExportService::class);
        $result = $service->executeImport($this->supplier, $importData, 'duplicate');

        $this->assertDatabaseHas('clt_layups', ['name' => 'Existing Layup (imported)']);
        $this->assertEquals(2, CltLayup::where('supplier_id', $this->supplier->id)->count());
    }
}
