<?php

namespace Database\Seeders;

use App\Models\Utility\User;
use App\Models\Utility\Warehouse;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WarehouseMigrateSeeder extends Seeder
{
    public function run(): void
    {
        $oldWarehouses = DB::table('hlokasi')
            ->select('id', 'kd_cab', 'kdlok', 'nmlokasi', 'aktif', 'userentry', 'tglentry', 'tglupdate')
            ->get();

        foreach ($oldWarehouses as $oldWarehouse) {
            Warehouse::query()->updateOrCreate(
                ['code' => $oldWarehouse->kdlok],
                [
                    'name' => $oldWarehouse->nmlokasi,
                    'branch_code' => $oldWarehouse->kd_cab ?? '00',
                    'is_active' => $oldWarehouse->aktif === 1,
                    'created_at' => $oldWarehouse->tglentry ?? now(),
                    'updated_at' => $oldWarehouse->tglupdate ?? now(),
                ]
            );
        }

        $oldPivots = DB::table('mhaklok')
            ->select('idmhaklok', 'nmuser', 'kdlok', 'aktif')
            ->get();

        foreach ($oldPivots as $oldPivot) {
            $user = User::where('name', $oldPivot->nmuser)->first();
            $warehouse = Warehouse::where('code', $oldPivot->kdlok)->first();

            if (! $user || ! $warehouse) {
                continue;
            }

            DB::table('user_warehouse')->updateOrInsert(
                [
                    'user_id' => $user->id,
                    'warehouse_id' => $warehouse->id,
                ],
                [
                    'is_active' => $oldPivot->aktif === 1,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
