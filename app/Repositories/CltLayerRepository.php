<?php

namespace App\Repositories;

use App\Models\CltLayer;
use App\Repositories\Interfaces\CltLayerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CltLayerRepository implements CltLayerRepositoryInterface
{
    public function allByLayup(int $layupId): Collection
    {
        return CltLayer::where('layup_id', $layupId)->orderBy('layer_order')->get();
    }

    public function paginate(int $perPage = 10, ?string $search = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = CltLayer::with('layup.supplier');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('layup', function ($q2) use ($search) {
                    $q2->where('name', 'like', '%' . $search . '%');
                })->orWhereHas('layup.supplier', function ($q2) use ($search) {
                    $q2->where('name', 'like', '%' . $search . '%');
                });
            });
        }

        return $query->orderBy('layup_id')->orderBy('layer_order')->paginate($perPage);
    }

    public function find(int $id): CltLayer
    {
        return CltLayer::findOrFail($id);
    }

    public function create(array $data): CltLayer
    {
        return CltLayer::create($data);
    }

    public function update(CltLayer $layer, array $data): CltLayer
    {
        $layer->update($data);
        return $layer;
    }

    public function delete(CltLayer $layer): void
    {
        $layer->delete();
    }
}
