<?php

namespace App\Services;

use App\Models\CltLayer;
use App\Repositories\Interfaces\CltLayerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CltLayerService
{
    public function __construct(
        private CltLayerRepositoryInterface $layerRepository
    ) {}

    public function allByLayup(int $layupId): Collection
    {
        return $this->layerRepository->allByLayup($layupId);
    }

    public function paginate(int $perPage = 10, ?string $search = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->layerRepository->paginate($perPage, $search);
    }

    public function find(int $id): CltLayer
    {
        return $this->layerRepository->find($id);
    }

    public function create(array $data): CltLayer
    {
        return $this->layerRepository->create($data);
    }

    public function update(CltLayer $layer, array $data): CltLayer
    {
        return $this->layerRepository->update($layer, $data);
    }

    public function delete(CltLayer $layer): void
    {
        $this->layerRepository->delete($layer);
    }
}
