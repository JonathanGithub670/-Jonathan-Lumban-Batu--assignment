<?php

namespace Tests\Unit;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Services\ImportExportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ImportExportServiceTest extends TestCase
{
    use RefreshDatabase;

    private ImportExportService $service;
    private Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ImportExportService::class);
        $this->supplier = Supplier::factory()->create(['name' => 'Test Supplier']);
    }

    public function test_export_returns_correct_structure(): void
    {
        $layup = CltLayup::factory()->create([
            'supplier_id' => $this->supplier->id,
            'name' => 'Wall Panel',
        ]);
        CltLayer::factory()->create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 40.00,
            'width' => 1200.00,
            'angle' => 0.00,
        ]);

        $result = $this->service->export($this->supplier);

        $this->assertArrayHasKey('supplier', $result);
        $this->assertArrayHasKey('layups', $result);
        $this->assertEquals('Test Supplier', $result['supplier']['name']);
        $this->assertCount(1, $result['layups']);
        $this->assertEquals('Wall Panel', $result['layups'][0]['name']);
        $this->assertCount(1, $result['layups'][0]['layers']);
        $this->assertEquals(40.0, $result['layups'][0]['layers'][0]['thickness']);
    }

    public function test_analyze_detects_new_layups(): void
    {
        $data = [
            'layups' => [
                ['name' => 'Brand New Layup', 'layers' => [
                    ['layer_order' => 1, 'thickness' => 20, 'width' => 100, 'angle' => 0],
                ]],
            ],
        ];

        $analysis = $this->service->analyzeImport($this->supplier, $data);

        $this->assertCount(1, $analysis['new_layups']);
        $this->assertEmpty($analysis['conflicts']);
    }

    public function test_analyze_detects_layer_conflicts(): void
    {
        $layup = CltLayup::factory()->create([
            'supplier_id' => $this->supplier->id,
            'name' => 'Existing',
        ]);
        CltLayer::factory()->create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 20.00,
            'width' => 100.00,
            'angle' => 0.00,
        ]);

        $data = [
            'layups' => [
                ['name' => 'Existing', 'layers' => [
                    ['layer_order' => 1, 'thickness' => 35, 'width' => 100, 'angle' => 0],
                ]],
            ],
        ];

        $analysis = $this->service->analyzeImport($this->supplier, $data);

        $this->assertCount(1, $analysis['conflicts']);
        $this->assertEquals(1, count($analysis['conflicts'][0]['layer_conflicts']));
    }

    public function test_analyze_no_conflict_when_data_matches(): void
    {
        $layup = CltLayup::factory()->create([
            'supplier_id' => $this->supplier->id,
            'name' => 'Existing',
        ]);
        CltLayer::factory()->create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 20.00,
            'width' => 100.00,
            'angle' => 0.00,
        ]);

        $data = [
            'layups' => [
                ['name' => 'Existing', 'layers' => [
                    ['layer_order' => 1, 'thickness' => 20.0, 'width' => 100.0, 'angle' => 0.0],
                ]],
            ],
        ];

        $analysis = $this->service->analyzeImport($this->supplier, $data);

        $this->assertEmpty($analysis['conflicts']);
    }

    public function test_reject_strategy_aborts_import(): void
    {
        $layup = CltLayup::factory()->create([
            'supplier_id' => $this->supplier->id,
            'name' => 'Existing',
        ]);
        CltLayer::factory()->create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 20.00,
            'width' => 100.00,
            'angle' => 0.00,
        ]);

        $data = [
            'layups' => [
                ['name' => 'Existing', 'layers' => [
                    ['layer_order' => 1, 'thickness' => 50, 'width' => 200, 'angle' => 45],
                ]],
            ],
        ];

        $result = $this->service->executeImport($this->supplier, $data, 'reject');

        $this->assertTrue($result['rejected']);
        $this->assertEquals(0, $result['created']);
        $this->assertEquals(0, $result['updated']);
        // Original data should be unchanged
        $this->assertDatabaseHas('clt_layers', [
            'layup_id' => $layup->id,
            'thickness' => 20.00,
        ]);
    }

    public function test_manual_strategy_applies_per_conflict_resolution(): void
    {
        $layup = CltLayup::factory()->create([
            'supplier_id' => $this->supplier->id,
            'name' => 'Existing',
        ]);
        $layer1 = CltLayer::factory()->create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 20.00,
            'width' => 100.00,
            'angle' => 0.00,
        ]);
        $layer2 = CltLayer::factory()->create([
            'layup_id' => $layup->id,
            'layer_order' => 2,
            'thickness' => 30.00,
            'width' => 150.00,
            'angle' => 90.00,
        ]);

        $data = [
            'layups' => [
                ['name' => 'Existing', 'layers' => [
                    ['layer_order' => 1, 'thickness' => 50, 'width' => 200, 'angle' => 45],
                    ['layer_order' => 2, 'thickness' => 60, 'width' => 250, 'angle' => 0],
                ]],
            ],
        ];

        $resolutions = [
            $layup->id . '_1' => 'incoming',  // Accept incoming for layer 1
            $layup->id . '_2' => 'existing',  // Keep existing for layer 2
        ];

        $result = $this->service->executeImport($this->supplier, $data, 'manual', $resolutions);

        $this->assertEquals(1, $result['updated']);
        $this->assertEquals(1, $result['skipped']);

        // Layer 1 should be updated
        $this->assertDatabaseHas('clt_layers', [
            'id' => $layer1->id,
            'thickness' => 50.00,
        ]);
        // Layer 2 should remain unchanged
        $this->assertDatabaseHas('clt_layers', [
            'id' => $layer2->id,
            'thickness' => 30.00,
        ]);
    }

    public function test_import_creates_new_layers_in_existing_layup(): void
    {
        $layup = CltLayup::factory()->create([
            'supplier_id' => $this->supplier->id,
            'name' => 'Existing',
        ]);
        CltLayer::factory()->create([
            'layup_id' => $layup->id,
            'layer_order' => 1,
            'thickness' => 20.00,
            'width' => 100.00,
            'angle' => 0.00,
        ]);

        $data = [
            'layups' => [
                ['name' => 'Existing', 'layers' => [
                    ['layer_order' => 1, 'thickness' => 20.0, 'width' => 100.0, 'angle' => 0.0],
                    ['layer_order' => 2, 'thickness' => 30.0, 'width' => 150.0, 'angle' => 90.0],
                ]],
            ],
        ];

        $result = $this->service->executeImport($this->supplier, $data);

        $this->assertEquals(1, $result['created']);
        $this->assertDatabaseHas('clt_layers', [
            'layup_id' => $layup->id,
            'layer_order' => 2,
            'thickness' => 30.00,
        ]);
    }
}
