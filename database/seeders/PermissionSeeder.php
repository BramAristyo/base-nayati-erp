<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        Cache::flush();

        $permissions = [
            // ── MASTER ────────────────────────────────────────
            'master.menu',

            'master.department.view',
            'master.department.create',
            'master.department.edit',
            'master.department.delete',

            'master.branch.view',
            'master.currency.view',
            'master.customer.view',
            'master.delivery-term.view',

            'master.employee.view',
            'master.employee.create',
            'master.employee.edit',
            'master.employee.delete',

            'master.supplier.view',
            'master.supplier.create',
            'master.supplier.edit',
            'master.supplier.delete',

            'master.product.view',
            'master.product.create',
            'master.product.edit',
            'master.product.delete',

            'master.product.variant.view',
            'master.product.variant.create',
            'master.product.variant.edit',
            'master.product.variant.delete',

            // ── PURCHASING — REQUEST ───────────────────────────
            'purchasing.menu',

            'purchasing.request.view',
            'purchasing.request.create',
            'purchasing.request.edit',
            'purchasing.request.delete',
            'purchasing.request.print',
            'purchasing.request.export',

            // ── PURCHASING — ORDER ─────────────────────────────
            'purchasing.order.view',
            'purchasing.order.create',
            'purchasing.order.edit',
            'purchasing.order.delete',
            'purchasing.order.print',
            'purchasing.order.export',

            // ── PURCHASING — RECEIVING ─────────────────────────
            'purchasing.receiving.view',
            'purchasing.receiving.create',
            'purchasing.receiving.edit',
            'purchasing.receiving.delete',
            'purchasing.receiving.print',
            'purchasing.receiving.print-with-price',
            'purchasing.receiving.export',

            // ── PURCHASING — APPROVAL ──────────────────────────
            'purchasing.approval.view',
            'purchasing.approval.purchase-request',
            'purchasing.approval.purchase-order',
            'purchasing.approval.receiving',

            // ── SALES ─────────────────────────────────────────
            'sales.menu',

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
            'reports.menu',
            'reports.purchasing',

            // ── UTILITY ───────────────────────────────────────
            'utility.menu',

            'utility.user.view',
            'utility.user.create',
            'utility.user.edit',
            'utility.user.delete',

            'utility.role.view',
            'utility.role.create',
            'utility.role.edit',
            'utility.role.delete',

            'utility.menu.view',
            'utility.menu.create',
            'utility.menu.edit',
            'utility.menu.delete',

            'utility.activity-log.view',
        ];

        foreach ($permissions as $permission) {
            Permission::query()->firstOrCreate(['name' => $permission]);
        }
    }
}
