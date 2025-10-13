<?php

namespace App\Http\Controllers\Dev;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * DEVELOPMENT ONLY CONTROLLER
 * This controller should only be used in development environment
 * Remove or disable in production
 */
class DevController extends Controller
{
    public function __construct()
    {
        // Only allow in development environment
        if (app()->environment('production')) {
            abort(404, 'Development features are disabled in production');
        }
    }

    /**
     * Auto login as super admin for development
     */
    public function autoLogin()
    {
        // Find or create super admin user
        $superAdmin = User::where('email', 'superadmin@laparchicken.com')->first();
        
        if (!$superAdmin) {
            $superAdmin = User::create([
                'name' => 'Super Admin (Dev)',
                'email' => 'superadmin@laparchicken.com',
                'password' => bcrypt('password'),
                'is_active' => true,
                'branch_id' => null // Super admin tidak terikat cabang
            ]);
        }
        
        // Login as super admin
        Auth::login($superAdmin);
        
        return redirect()->route('dashboard')->with('success', 'Logged in as Super Admin for development');
    }
    
    /**
     * Logout
     */
    public function logout()
    {
        Auth::logout();
        return redirect()->route('dev.auto-login')->with('success', 'Logged out');
    }
}
