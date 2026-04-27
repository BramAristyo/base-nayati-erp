<?php

declare(strict_types=1);

namespace App\Http\Controllers\Approval;

use App\Http\Controllers\Controller;
use Illuminate\Routing\Attributes\Controllers\Middleware;
use Inertia\Inertia;
use Inertia\Response;

class ApprovalController extends Controller
{
    #[Middleware('can:approval.approval.view')]
    public function index(): Response
    {
        return Inertia::render('Approval/Index');
    }
}
