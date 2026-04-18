<?php

namespace App\Services;

use App\Models\CltLayup;
use App\Repositories\Interfaces\CltLayupRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CltLayupService
{
    public function __construct(
        private CltLayupRepositoryInterface $layupRepository
    ) {}

    public function allBySupplier(int $supplierId): Collection
    {
        return $this->layupRepository->allBySupplier($supplierId);
    }

    public function paginate(int $perPage = 10, ?string $search = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->layupRepository->paginate($perPage, $search);
    }

    public function find(int $id): CltLayup
    {
        return $this->layupRepository->find($id);
    }

    public function create(array $data): CltLayup
    {
        return $this->layupRepository->create($data);
    }

    public function update(CltLayup $layup, array $data): CltLayup
    {
        return $this->layupRepository->update($layup, $data);
    }

    public function duplicate(CltLayup $layup): CltLayup
    {
        return $this->layupRepository->duplicate($layup);
    }

    public function delete(CltLayup $layup): void
    {
        $this->layupRepository->delete($layup);
    }
}
