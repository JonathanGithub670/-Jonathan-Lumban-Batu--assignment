<?php

namespace App\Repositories\Interfaces;

use App\Models\CltLayer;
use Illuminate\Database\Eloquent\Collection;

interface CltLayerRepositoryInterface
{
    public function allByLayup(int $layupId): Collection;
    public function paginate(int $perPage = 10, ?string $search = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator;
    public function find(int $id): CltLayer;
    public function create(array $data): CltLayer;
    public function update(CltLayer $layer, array $data): CltLayer;
    public function delete(CltLayer $layer): void;
}
