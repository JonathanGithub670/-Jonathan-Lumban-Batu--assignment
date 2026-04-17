<?php

namespace Tests\Feature;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CltLayerCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Supplier $supplier;
    private CltLayup $layup;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->supplier = Supplier::factory()->create();
        $this->layup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id]);
    }

    public function test_can_create_layer(): void
    {
        $response = $this->actingAs($this->user)->post(
            route('suppliers.layups.layers.store', [$this->supplier, $this->layup]),
            [
                'layer_order' => 1,
                'thickness' => 20.5,
                'width' => 100.0,
                'angle' => 0.0,
            ]
        );

        $response->assertRedirect(route('suppliers.layups.show', [$this->supplier, $this->layup]));
        $this->assertDatabaseHas('clt_layers', [
            'layup_id' => $this->layup->id,
            'layer_order' => 1,
        ]);
    }

    public function test_create_layer_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user)->post(
            route('suppliers.layups.layers.store', [$this->supplier, $this->layup]),
            []
        );

        $response->assertSessionHasErrors(['layer_order', 'thickness', 'width', 'angle']);
    }

    public function test_can_update_layer(): void
    {
        $layer = CltLayer::factory()->create(['layup_id' => $this->layup->id]);

        $response = $this->actingAs($this->user)->put(
            route('suppliers.layups.layers.update', [$this->supplier, $this->layup, $layer]),
            [
                'layer_order' => 5,
                'thickness' => 30.0,
                'width' => 200.0,
                'angle' => 45.0,
            ]
        );

        $response->assertRedirect(route('suppliers.layups.show', [$this->supplier, $this->layup]));
        $this->assertDatabaseHas('clt_layers', ['id' => $layer->id, 'layer_order' => 5]);
    }

    public function test_can_delete_layer(): void
    {
        $layer = CltLayer::factory()->create(['layup_id' => $this->layup->id]);

        $response = $this->actingAs($this->user)->delete(
            route('suppliers.layups.layers.destroy', [$this->supplier, $this->layup, $layer])
        );

        $response->assertRedirect(route('suppliers.layups.show', [$this->supplier, $this->layup]));
        $this->assertDatabaseMissing('clt_layers', ['id' => $layer->id]);
    }
}
