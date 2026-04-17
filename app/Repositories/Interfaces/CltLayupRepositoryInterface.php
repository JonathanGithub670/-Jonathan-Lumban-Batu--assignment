<?php

namespace App\Repositories\Interfaces;

use App\Models\CltLayup;
use Illuminate\Database\Eloquent\Collection;

interface CltLayupRepositoryInterface
{
    public function allBySupplier(int $supplierId): Collection;
    public function paginate(int $perPage = 10, ?string $search = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator;
    public function find(int $id): CltLayup;
    public function create(array $data): CltLayup;
    public function update(CltLayup $layup, array $data): CltLayup;
    public function duplicate(CltLayup $layup): CltLayup;
    public function delete(CltLayup $layup): void;
}
