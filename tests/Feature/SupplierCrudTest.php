<?php

namespace Tests\Feature;

use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierCrudTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_view_suppliers_index(): void
    {
        Supplier::factory()->count(3)->create();

        $response = $this->actingAs($this->user)->get(route('suppliers.index'));

        $response->assertStatus(200);
        $response->assertViewIs('suppliers.index');
    }

    public function test_can_create_supplier(): void
    {
        $response = $this->actingAs($this->user)->post(route('suppliers.store'), [
            'name' => 'Test Supplier',
        ]);

        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseHas('suppliers', ['name' => 'Test Supplier']);
    }

    public function test_create_supplier_requires_name(): void
    {
        $response = $this->actingAs($this->user)->post(route('suppliers.store'), [
            'name' => '',
        ]);

        $response->assertSessionHasErrors('name');
    }

    public function test_can_update_supplier(): void
    {
        $supplier = Supplier::factory()->create(['name' => 'Old Name']);

        $response = $this->actingAs($this->user)->put(route('suppliers.update', $supplier), [
            'name' => 'New Name',
        ]);

        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'name' => 'New Name']);
    }

    public function test_can_delete_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('suppliers.destroy', $supplier));

        $response->assertRedirect(route('suppliers.index'));
        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    public function test_can_view_supplier_show(): void
    {
        $supplier = Supplier::factory()->create();

        $response = $this->actingAs($this->user)->get(route('suppliers.show', $supplier));

        $response->assertStatus(200);
        $response->assertViewIs('suppliers.show');
    }
}
