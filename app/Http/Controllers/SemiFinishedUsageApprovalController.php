<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\SemiFinishedUsageRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SemiFinishedUsageApprovalController extends Controller
{
    /**
     * Approvals inbox for semi-finished usage requests.
     */
    public function index(Request $request)
    {
        $query = SemiFinishedUsageRequest::query()
            ->with(['requestingBranch', 'requestedBy', 'approvedByUser'])
            ->orderBy('created_at', 'desc');

        // Status filter (default: pending)
        $status = $request->get('status', SemiFinishedUsageRequest::STATUS_PENDING);
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Branch filter
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('super-admin') || $user->hasRole('Super Admin') || $user->hasRole('Manager');
        $isAdmin = $user->hasRole('admin') || $user->hasRole('Admin') || $user->hasRole('Kepala Toko');

        if ($isSuperAdmin || $isAdmin) {
            if ($request->filled('branch_id') && $request->branch_id !== 'all') {
                $query->where('requesting_branch_id', $request->branch_id);
            } else {
                // If there's a current branch context, default to it
                $branchId = app()->bound('current_branch_id') ? app('current_branch_id') : ($user->branch_id ?? null);
                if ($branchId) {
                    $query->where('requesting_branch_id', $branchId);
                }
            }
        } else {
            // Regular users: only their branch
            $branchId = app()->bound('current_branch_id') ? app('current_branch_id') : ($user->branch_id ?? null);
            if ($branchId) {
                $query->where('requesting_branch_id', $branchId);
            }
        }

        $requests = $query->paginate(15);
        $branches = Branch::orderBy('name')->get();

        return view('semi-finished-usage-approvals.index', compact('requests', 'branches', 'status'));
    }

    /**
     * Redirect to the underlying request detail page for unified actions.
     */
    public function show(SemiFinishedUsageRequest $semi_finished_usage_approval)
    {
        return redirect()->route('semi-finished-usage-requests.show', $semi_finished_usage_approval);
    }
}