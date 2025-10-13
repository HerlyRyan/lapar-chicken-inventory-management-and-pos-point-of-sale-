<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Branch;

class BranchContextMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        if ($user) {
            // Handle branch selection for Super Admin
            if ($user->hasRole('Super Admin')) {
                // Store branch selection in session only (bypass database)
                if ($request->filled('branch_id')) {
                    // Store in session for immediate access
                    session([
                        'selected_branch_id' => $request->branch_id,
                        'session_id' => 'dev_session_' . time(),
                        'user_id' => $user->id
                    ]);
                }
                
                // Get current branch selection from multiple sources
                $selectedBranchId = null;
                
                // Priority 1: URL parameter
                if ($request->filled('branch_id')) {
                    $selectedBranchId = $request->branch_id;
                    
                    // Store in session to maintain during navigation
                    session(['selected_branch_id' => $selectedBranchId]);
                }
                // Priority 2: Use session data
                elseif (session()->has('selected_branch_id')) {
                    $selectedBranchId = session('selected_branch_id');
                    
                    // Make sure session has all we need
                    session([
                        'session_id' => 'dev_session_' . time(),
                        'user_id' => $user->id
                    ]);
                }
                
                $selectedBranch = $selectedBranchId ? Branch::find($selectedBranchId) : null;
                
                // Share with all views
                view()->share('selectedBranch', $selectedBranch);
                view()->share('showBranchSelector', true);
                view()->share('branches', Branch::where('is_active', true)->get());
                view()->share('canSwitchBranch', true);
            } else {
                // Staff: use their assigned branch
                $userBranch = $user->branch;
                view()->share('selectedBranch', $userBranch);
                view()->share('currentBranch', $userBranch);
                view()->share('showBranchSelector', false);
                view()->share('canSwitchBranch', false);
            }
        } else {
            // Development fallback: when auth is disabled
            $selectedBranchId = null;
            $selectedBranch = null;
            
            // Only set specific branch if branch_id is explicitly provided in URL
            if ($request->filled('branch_id')) {
                $selectedBranchId = $request->branch_id;
                $selectedBranch = Branch::where('is_active', true)->find($selectedBranchId);
                
                // Store in session for persistence across all pages
                if ($selectedBranch) {
                    session([
                        'selected_branch_id' => $selectedBranchId,
                        'session_id' => 'dev_session_' . time()
                    ]);
                }
            } 
            // Check session only if no URL parameter (for navigation persistence)
            elseif (session()->has('selected_branch_id')) {
                $selectedBranchId = session('selected_branch_id');
                $selectedBranch = Branch::where('is_active', true)->find($selectedBranchId);
                
                // If branch doesn't exist anymore, remove it from session
                if (!$selectedBranch) {
                    session()->forget('selected_branch_id');
                }
            }
            
            // Make sure we have a branch selected for development
            if (!$selectedBranch) {
                $selectedBranch = Branch::where('is_active', true)->first();
                if ($selectedBranch) {
                    $selectedBranchId = $selectedBranch->id;
                    session([
                        'selected_branch_id' => $selectedBranchId,
                        'session_id' => 'dev_session_' . time()
                    ]);
                }
            }
            
            view()->share('selectedBranch', $selectedBranch);
            view()->share('currentBranch', $selectedBranch);
            view()->share('showBranchSelector', true);
            view()->share('branches', Branch::where('is_active', true)->get());
            view()->share('canSwitchBranch', true);
        }

        return $next($request);
    }
}
