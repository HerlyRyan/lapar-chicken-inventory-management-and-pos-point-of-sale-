<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Branch;

trait ResolvesBranchContext
{
    /**
     * Resolve the active/selected branch context based on user role and request/session.
     */
    protected function resolveSelectedBranch(Request $request): ?Branch
    {
        $user = Auth::user();
        if ($user && !$user->hasRole('Super Admin')) {
            return $user->branch; // enforce branch restriction for non super admin
        }

        // Use the same session key as dashboard/BranchComposer to keep branch selection consistent
        $selectedBranchId = $request->input('branch_id', session('selected_dashboard_branch'));
        if ($selectedBranchId) {
            return Branch::active()->find($selectedBranchId);
        }

        return null;
    }
}
