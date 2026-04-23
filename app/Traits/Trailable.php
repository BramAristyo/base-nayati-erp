<?php

namespace App\Traits;

use App\Enums\LogAction;
use App\Enums\LogDetailRoute;
use App\Enums\LogModule;
use App\Models\Utility\AuditTrail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait Trailable
{
    protected function trail(
        LogModule $module,
        LogAction $action,
        string $description,
        int|string|null $subjectId = null,
        LogDetailRoute | null $detailRoute = null
    ): void {
        try {
            AuditTrail::create([
                'causer_id'   => Auth::id(),
                'action'      => $action->value,
                'description' => $description,
                'subject_type'=> $module->value,
                'subject_id'  => $subjectId,
                'detail_route' => $detailRoute->value,
            ]);
        } catch (\Throwable $e) {
            Log::error('Trail failed: ' . $e->getMessage());
        }
    }
}