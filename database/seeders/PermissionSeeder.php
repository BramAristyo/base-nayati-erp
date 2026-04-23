<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use App\Models\Utility\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        Cache::flush();

        $permissions = [
            // ── MASTER ────────────────────────────────────────
            'master.department.view',
            'master.branch.view',
            'master.currency.view',
            'master.customer.view',
            'master.delivery-term.view',
            'master.employee.view',
            'master.supplier.view',
            'master.product.view',

            // ── PURCHASING — REQUEST ───────────────────────────
            'purchasing.purchase-request.view',
            'purchasing.purchase-request.create',
            'purchasing.purchase-request.edit',
            'purchasing.purchase-request.delete',
            'purchasing.purchase-request.print',
            'purchasing.purchase-request.export',

            // ── PURCHASING — ORDER ─────────────────────────────
            'purchasing.purchase-order.view',
            'purchasing.purchase-order.create',
            'purchasing.purchase-order.edit',
            'purchasing.purchase-order.delete',
            'purchasing.purchase-order.print',
            'purchasing.purchase-order.export',

            // ── PURCHASING — RECEIVING ─────────────────────────
            'purchasing.receiving.view',
            'purchasing.receiving.create',
            'purchasing.receiving.edit',
            'purchasing.receiving.delete',
            'purchasing.receiving.print',
            'purchasing.receiving.print-with-price',
            'purchasing.receiving.export',

            // ── PURCHASING — APPROVAL ──────────────────────────
            'approval.purchase-request.view',
            'approval.purchase-request.approve',
            'approval.purchase-request.reject',

            'approval.purchase-order.view',
            'approval.purchase-order.approve',
            'approval.purchase-order.reject',

            'approval.receiving.view',
            'approval.receiving.approve',
            'approval.receiving.reject',

            // ── SALES ─────────────────────────────────────────

            'sales.order.view',
            'sales.order.create',
            'sales.order.edit',
            'sales.order.delete',
            'sales.order.print',
            'sales.order.export',

            'sales.proforma.view',
            'sales.proforma.create',
            'sales.proforma.edit',
            'sales.proforma.delete',
            'sales.proforma.print',
            'sales.proforma.export',

            'sales.delivery-order.view',
            'sales.delivery-order.create',
            'sales.delivery-order.edit',
            'sales.delivery-order.delete',
            'sales.delivery-order.print',
            'sales.delivery-order.export',

            'sales.shipment.view',
            'sales.shipment.create',
            'sales.shipment.edit',
            'sales.shipment.delete',
            'sales.shipment.print',
            'sales.shipment.export',

            'sales.invoice.view',
            'sales.invoice.create',
            'sales.invoice.edit',
            'sales.invoice.delete',
            'sales.invoice.print',
            'sales.invoice.export',

            // ── REPORTS ────────────────────────────────────────
            'reports.purchasing.view',
            'reports.purchasing.export',

            // ── UTILITY ───────────────────────────────────────
            'utility.user.view',
            'utility.user.create',
            'utility.user.edit',
            'utility.user.delete',

            'utility.role.view',
            'utility.role.create',
            'utility.role.edit',
            'utility.role.delete',

            'utility.audit-trail.view',
        ];

        foreach ($permissions as $slug) {
            $parts = explode('.', $slug);
            $module = ucfirst(array_shift($parts)); 
            $action = count($parts) > 0 ? ucfirst(array_pop($parts)) : 'Menu'; 
            $subModule = count($parts) > 0 ? ucwords(str_replace('-', ' ', implode(' ', $parts))) : null;
            
            Permission::query()->firstOrCreate(
                ['slug' => $slug],
                [
                    'module' => $module,
                    'sub_module' => $subModule,
                    'action' => $action
                ]
            );
        }
    }
}
