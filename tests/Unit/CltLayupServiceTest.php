<?php

namespace Tests\Unit;

use App\Models\CltLayup;
use App\Models\Supplier;
use App\Services\CltLayupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CltLayupServiceTest extends TestCase
{
    use RefreshDatabase;

    private CltLayupService $service;
    private Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(CltLayupService::class);
        $this->supplier = Supplier::factory()->create();
    }

    public function test_can_create_layup(): void
    {
        $layup = $this->service->create([
            'supplier_id' => $this->supplier->id,
            'name' => 'Test Layup',
        ]);

        $this->assertInstanceOf(CltLayup::class, $layup);
        $this->assertEquals('Test Layup', $layup->name);
        $this->assertEquals($this->supplier->id, $layup->supplier_id);
    }

    public function test_can_update_layup(): void
    {
        $layup = CltLayup::factory()->create([
            'supplier_id' => $this->supplier->id,
            'name' => 'Old Name',
        ]);

        $updated = $this->service->update($layup, ['name' => 'New Name']);

        $this->assertEquals('New Name', $updated->name);
    }

    public function test_can_delete_layup(): void
    {
        $layup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id]);

        $this->service->delete($layup);

        $this->assertDatabaseMissing('clt_layups', ['id' => $layup->id]);
    }
}
