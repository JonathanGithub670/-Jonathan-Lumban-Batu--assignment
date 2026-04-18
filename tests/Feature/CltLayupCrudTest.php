<?php

namespace Tests\Feature;

use App\Models\CltLayup;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CltLayupCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Supplier $supplier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->supplier = Supplier::factory()->create();
    }

    public function test_can_create_layup(): void
    {
        $response = $this->actingAs($this->user)->post(
            route('suppliers.layups.store', $this->supplier),
            ['name' => 'Test Layup']
        );

        $response->assertRedirect(route('suppliers.show', $this->supplier));
        $this->assertDatabaseHas('clt_layups', [
            'supplier_id' => $this->supplier->id,
            'name' => 'Test Layup',
        ]);
    }

    public function test_can_update_layup(): void
    {
        $layup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id]);

        $response = $this->actingAs($this->user)->put(
            route('suppliers.layups.update', [$this->supplier, $layup]),
            ['name' => 'Updated Layup']
        );

        $response->assertRedirect(route('suppliers.layups.show', [$this->supplier, $layup]));
        $this->assertDatabaseHas('clt_layups', ['id' => $layup->id, 'name' => 'Updated Layup']);
    }

    public function test_can_delete_layup(): void
    {
        $layup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id]);

        $response = $this->actingAs($this->user)->delete(
            route('suppliers.layups.destroy', [$this->supplier, $layup])
        );

        $response->assertRedirect(route('suppliers.show', $this->supplier));
        $this->assertDatabaseMissing('clt_layups', ['id' => $layup->id]);
    }

    public function test_can_view_layup_show(): void
    {
        $layup = CltLayup::factory()->create(['supplier_id' => $this->supplier->id]);

        $response = $this->actingAs($this->user)->get(
            route('suppliers.layups.show', [$this->supplier, $layup])
        );

        $response->assertStatus(200);
        $response->assertViewIs('layups.show');
    }
}
