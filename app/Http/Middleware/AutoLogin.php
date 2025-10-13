<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\Branch;

class AutoLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Only auto-login if not already logged in
        if (!Auth::check()) {
            // Find admin user
            $admin = User::where('email', 'admin.laparchicken@gmail.com')->first();
            
            if ($admin) {
                Auth::login($admin);
            } else {
                // Fallback to first user in database if admin not found
                $firstUser = User::first();
                if ($firstUser) {
                    Auth::login($firstUser);
                }
            }
        }
        
        // Handle branch sessions - bypass the database user_branch_sessions table
        if (Auth::check()) {
            // Set branch session in the session directly
            if (!$request->session()->has('branch_id')) {
                // Find an active branch
                $branch = Branch::where('is_active', true)->first();
                if ($branch) {
                    $request->session()->put('branch_id', $branch->id);
                    $request->session()->put('session_id', 'dev_session_' . time());
                    
                    // Store user ID in session
                    $request->session()->put('user_id', Auth::id());
                }
            }
        }
        
        return $next($request);
    }
}
