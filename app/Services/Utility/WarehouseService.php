<?php

namespace App\Services\Utility;

use App\Models\Utility\Warehouse;
use Illuminate\Database\Eloquent\Collection;

class WarehouseService
{
    public function getAll(): Collection
    {
        return Warehouse::active()->get();
    }
}
