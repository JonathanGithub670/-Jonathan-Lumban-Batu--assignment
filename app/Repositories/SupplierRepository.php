<?php

namespace App\Repositories;

use App\Models\Supplier;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SupplierRepository implements SupplierRepositoryInterface
{
    public function all(): Collection
    {
        return Supplier::all();
    }

    public function paginate(int $perPage = 10, ?string $search = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = Supplier::withCount('layups');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
                
                // Allow searching by numeric ID if the search input is numeric
                if (is_numeric($search)) {
                    $q->orWhere('id', $search);
                }
                
                // Also support searching by the formatted ID "SUP-YYYY-ID"
                if (preg_match('/SUP-\d{4}-(\d+)/i', $search, $matches)) {
                    $q->orWhere('id', (int)$matches[1]);
                }
            });
        }

        return $query->latest()->paginate($perPage);
    }

    public function find(int $id): Supplier
    {
        return Supplier::findOrFail($id);
    }

    public function create(array $data): Supplier
    {
        return Supplier::create($data);
    }

    public function update(Supplier $supplier, array $data): Supplier
    {
        $supplier->update($data);
        return $supplier;
    }

    public function delete(Supplier $supplier): void
    {
        $supplier->delete();
    }

    public function getWithLayupsAndLayers(int $id): Supplier
    {
        return Supplier::with('layups.layers')->findOrFail($id);
    }
}
