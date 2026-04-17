<?php

namespace App\Repositories;

use App\Models\CltLayup;
use App\Repositories\Interfaces\CltLayupRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CltLayupRepository implements CltLayupRepositoryInterface
{
    public function allBySupplier(int $supplierId): Collection
    {
        return CltLayup::where('supplier_id', $supplierId)->with('layers')->get();
    }

    public function paginate(int $perPage = 10, ?string $search = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = CltLayup::with('supplier')->withCount('layers');

        if ($search) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        return $query->latest()->paginate($perPage);
    }

    public function find(int $id): CltLayup
    {
        return CltLayup::with('layers')->findOrFail($id);
    }

    public function create(array $data): CltLayup
    {
        return CltLayup::create($data);
    }

    public function update(CltLayup $layup, array $data): CltLayup
    {
        $layup->update($data);
        return $layup;
    }

    public function duplicate(CltLayup $layup): CltLayup
    {
        // Clone the layup model
        $newLayup = $layup->replicate();
        $newLayup->name = $layup->name . ' (Copy)';
        $newLayup->identifier = $layup->identifier . '-COPY-' . now()->timestamp;
        $newLayup->save();

        // Clone all associated layers
        foreach ($layup->layers as $layer) {
            $newLayer = $layer->replicate();
            $newLayer->layup_id = $newLayup->id;
            $newLayer->save();
        }

        return $newLayup;
    }

    public function delete(CltLayup $layup): void
    {
        $layup->delete();
    }
}
