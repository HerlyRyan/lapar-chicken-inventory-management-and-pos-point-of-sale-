<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BranchContext
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Default values for unauthenticated or incomplete profiles
            $currentBranch = null;
            $isMultiBranch = \App\Models\Branch::count() > 1;
            $branches = collect([]);
            $canSwitchBranch = false;

            if (auth()->check() && auth()->user()) {
                $user = auth()->user();
                // Allow clearing current branch globally to enter overview mode
                if ($request->boolean('clear_branch') || $request->boolean('clear_dashboard_branch')) {
                    // Forget all possible legacy keys to ensure a true overview state
                    session()->forget(['current_branch_id', 'selected_branch_id', 'selected_dashboard_branch']);
                }
                // If branch_id is explicitly provided in query, allow switching context
                // Only allow Super Admins to freely switch; regular users only to their own branch
                $requestedBranchId = (int) ($request->query('branch_id') ?? 0);
                if ($requestedBranchId > 0) {
                    try {
                        $isSuperAdmin = method_exists($user, 'hasRole') && (
                            $user->hasRole('Super Admin') ||
                            $user->hasRole('super_admin') ||
                            $user->hasRole('super-admin')
                        );
                    } catch (\Throwable $e) { $isSuperAdmin = false; }
                    if ($isSuperAdmin || ((int) ($user->branch_id ?? 0) === $requestedBranchId)) {
                        session(['current_branch_id' => $requestedBranchId]);
                    }
                }
                
                // Get available branches based on user role
                if ($user->hasRole('Super Admin')) {
                    $branches = \App\Models\Branch::with(['users'])->get();
                    $canSwitchBranch = true;
                    
                    // Get branch strictly from session for Super Admins.
                    // If not set, remain in overview (no current branch selected).
                    $currentBranch = session('current_branch_id')
                        ? \App\Models\Branch::find(session('current_branch_id'))
                        : null;
                } else {
                    $currentBranch = $user->branch;
                    $branches = $currentBranch ? collect([$currentBranch]) : collect([]);
                    $canSwitchBranch = false;
                }
                
                if ($currentBranch) {
                    // Store branch context in application container
                    app()->instance('current_branch_id', $currentBranch->id);
                    app()->instance('current_branch', $currentBranch);
                }
            }

            // Share with all views
            view()->share([
                'currentBranch' => $currentBranch,
                'currentBranchId' => $currentBranch ? $currentBranch->id : null,
                'allBranches' => $branches,
                'canSwitchBranch' => $canSwitchBranch,
                'isMultiBranch' => $isMultiBranch,
                'userBranchId' => $currentBranch ? $currentBranch->id : null,
                'userBranchCode' => $currentBranch ? $currentBranch->code : 'N/A',
                // Aliases for header layout compatibility
                'selectedBranch' => $currentBranch,
                'branches' => $branches,
                'showBranchSelector' => $canSwitchBranch,
            ]);

            return $next($request);
            
        } catch (\Exception $e) {
            // In case of any error, provide safe defaults
            view()->share([
                'currentBranch' => null,
                'currentBranchId' => null,
                'allBranches' => collect([]),
                'canSwitchBranch' => false,
                'isMultiBranch' => false,
                'userBranchId' => null,
                'userBranchCode' => 'N/A',
                // Aliases for header layout compatibility
                'selectedBranch' => null,
                'branches' => collect([]),
                'showBranchSelector' => false,
            ]);

            return $next($request);
        }
    }
}
