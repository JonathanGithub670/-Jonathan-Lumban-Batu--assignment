<?php

namespace App\Repositories\Interfaces;

use App\Models\Supplier;
use Illuminate\Database\Eloquent\Collection;

interface SupplierRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 10, ?string $search = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator;
    public function find(int $id): Supplier;
    public function create(array $data): Supplier;
    public function update(Supplier $supplier, array $data): Supplier;
    public function delete(Supplier $supplier): void;
    public function getWithLayupsAndLayers(int $id): Supplier;
}
