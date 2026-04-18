<?php

namespace App\Services;

use App\Models\Supplier;
use App\Repositories\Interfaces\SupplierRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SupplierService
{
    public function __construct(
        private SupplierRepositoryInterface $supplierRepository
    ) {}

    public function all(): Collection
    {
        return $this->supplierRepository->all();
    }

    public function paginate(int $perPage = 10, ?string $search = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->supplierRepository->paginate($perPage, $search);
    }

    public function find(int $id): Supplier
    {
        return $this->supplierRepository->find($id);
    }

    public function create(array $data): Supplier
    {
        return $this->supplierRepository->create($data);
    }

    public function update(Supplier $supplier, array $data): Supplier
    {
        return $this->supplierRepository->update($supplier, $data);
    }

    public function delete(Supplier $supplier): void
    {
        $this->supplierRepository->delete($supplier);
    }

    public function getWithLayupsAndLayers(int $id): Supplier
    {
        return $this->supplierRepository->getWithLayupsAndLayers($id);
    }
}
