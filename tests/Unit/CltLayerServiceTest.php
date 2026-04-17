<?php

namespace Tests\Unit;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Services\CltLayerService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CltLayerServiceTest extends TestCase
{
    use RefreshDatabase;

    private CltLayerService $service;
    private CltLayup $layup;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(CltLayerService::class);
        $supplier = Supplier::factory()->create();
        $this->layup = CltLayup::factory()->create(['supplier_id' => $supplier->id]);
    }

    public function test_can_create_layer(): void
    {
        $layer = $this->service->create([
            'layup_id' => $this->layup->id,
            'layer_order' => 1,
            'thickness' => 40.00,
            'width' => 1200.00,
            'angle' => 0.00,
        ]);

        $this->assertInstanceOf(CltLayer::class, $layer);
        $this->assertEquals(1, $layer->layer_order);
        $this->assertEquals($this->layup->id, $layer->layup_id);
    }

    public function test_can_update_layer(): void
    {
        $layer = CltLayer::factory()->create(['layup_id' => $this->layup->id]);

        $updated = $this->service->update($layer, [
            'layer_order' => 2,
            'thickness' => 30.00,
            'width' => 150.00,
            'angle' => 90.00,
        ]);

        $this->assertEquals(2, $updated->layer_order);
        $this->assertEquals('30.00', $updated->thickness);
    }

    public function test_can_delete_layer(): void
    {
        $layer = CltLayer::factory()->create(['layup_id' => $this->layup->id]);

        $this->service->delete($layer);

        $this->assertDatabaseMissing('clt_layers', ['id' => $layer->id]);
    }
}
