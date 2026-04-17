<?php

namespace Tests\Unit;

use App\Models\Supplier;
use App\Models\User;
use App\Services\SupplierService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SupplierServiceTest extends TestCase
{
    use RefreshDatabase;

    private SupplierService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(SupplierService::class);
    }

    public function test_can_create_supplier(): void
    {
        $supplier = $this->service->create(['name' => 'Test Supplier']);

        $this->assertInstanceOf(Supplier::class, $supplier);
        $this->assertEquals('Test Supplier', $supplier->name);
        $this->assertDatabaseHas('suppliers', ['name' => 'Test Supplier']);
    }

    public function test_can_get_all_suppliers(): void
    {
        Supplier::factory()->count(3)->create();

        $suppliers = $this->service->all();

        $this->assertCount(3, $suppliers);
    }

    public function test_can_find_supplier(): void
    {
        $supplier = Supplier::factory()->create(['name' => 'Find Me']);

        $found = $this->service->find($supplier->id);

        $this->assertEquals('Find Me', $found->name);
    }

    public function test_can_update_supplier(): void
    {
        $supplier = Supplier::factory()->create(['name' => 'Old']);

        $updated = $this->service->update($supplier, ['name' => 'New']);

        $this->assertEquals('New', $updated->name);
        $this->assertDatabaseHas('suppliers', ['id' => $supplier->id, 'name' => 'New']);
    }

    public function test_can_delete_supplier(): void
    {
        $supplier = Supplier::factory()->create();

        $this->service->delete($supplier);

        $this->assertDatabaseMissing('suppliers', ['id' => $supplier->id]);
    }

    public function test_can_paginate_suppliers(): void
    {
        Supplier::factory()->count(15)->create();

        $paginated = $this->service->paginate(10);

        $this->assertEquals(10, $paginated->count());
        $this->assertEquals(15, $paginated->total());
    }

    public function test_can_search_suppliers(): void
    {
        Supplier::factory()->create(['name' => 'Nordic Timber']);
        Supplier::factory()->create(['name' => 'Alpine Solutions']);
        Supplier::factory()->create(['name' => 'Nordic Wood']);

        $results = $this->service->paginate(10, 'Nordic');

        $this->assertEquals(2, $results->total());
    }
}
